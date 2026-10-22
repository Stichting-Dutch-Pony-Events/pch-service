<?php

namespace App\Application\Service;

use App\Application\Request\LiveQuizCreateQuestionRequest;
use App\Application\Request\LiveQuizCreateSessionRequest;
use App\Application\Request\LiveQuizJoinSessionRequest;
use App\Application\Request\LiveQuizRespondRequest;
use App\Application\View\LiveQuizOptionResultView;
use App\Application\View\LiveQuizParticipantView;
use App\Application\View\LiveQuizQuestionView;
use App\Application\View\LiveQuizResultView;
use App\Application\View\LiveQuizSessionView;
use App\DataAccessLayer\Repository\LiveQuizParticipantRepository;
use App\DataAccessLayer\Repository\LiveQuizQuestionRepository;
use App\DataAccessLayer\Repository\LiveQuizResponseRepository;
use App\DataAccessLayer\Repository\LiveQuizSessionRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\LiveQuizOption;
use App\Domain\Entity\LiveQuizParticipant;
use App\Domain\Entity\LiveQuizQuestion;
use App\Domain\Entity\LiveQuizResponse;
use App\Domain\Entity\LiveQuizSession;
use App\Util\SymfonyUtils\Mapper;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

readonly class LiveQuizApplicationService
{
    public function __construct(
        private LiveQuizSessionRepository     $sessionRepository,
        private LiveQuizParticipantRepository $participantRepository,
        private LiveQuizQuestionRepository    $questionRepository,
        private LiveQuizResponseRepository    $responseRepository,
        private EntityManagerInterface        $entityManager,
        private Messaging                     $firebaseMessaging,
        private LoggerInterface               $logger,
    ) {
    }

    public function createSession(LiveQuizCreateSessionRequest $req, Attendee $staff): LiveQuizSessionView
    {
        $this->requireValid(trim($req->name) !== '' && mb_strlen($req->name) <= 200, 'Name must be 1-200 characters');
        $this->requireValid(mb_strlen(trim($req->password)) >= 3 && mb_strlen($req->password) <= 255, 'Password must be 3-255 characters');

        // Attendees join by password alone, so two active sessions can't share one.
        if ($this->sessionRepository->findActiveByPassword($req->password) !== null) {
            throw new ConflictHttpException('An active session already uses this password');
        }

        // Plain text on purpose: it's a join code staff read out to the room and need to look up again.
        $session = new LiveQuizSession(
            name: $req->name,
            password: $req->password,
            isActive: true,
            createdBy: $staff,
        );

        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $this->toSessionView($session);
    }

    public function joinSession(LiveQuizJoinSessionRequest $req, Attendee $attendee): LiveQuizParticipantView
    {
        $session = $this->sessionRepository->findActiveByPassword($req->password);

        if ($session === null) {
            throw new AccessDeniedHttpException('Invalid session password');
        }

        $existing = $this->participantRepository->findBySessionAndAttendee($session, $attendee);

        if ($existing !== null) {
            return Mapper::mapOne($existing, LiveQuizParticipantView::class);
        }

        $participant = new LiveQuizParticipant($session, $attendee, new DateTime());
        $this->entityManager->persist($participant);
        $this->flushUnique('Already joined this session');

        return Mapper::mapOne($participant, LiveQuizParticipantView::class);
    }

    public function getJoinedSession(Attendee $attendee): ?LiveQuizParticipantView
    {
        $participant = $this->participantRepository->findLatestActiveForAttendee($attendee);

        return $participant === null ? null : Mapper::mapOne($participant, LiveQuizParticipantView::class);
    }

    public function createQuestion(LiveQuizSession $session, LiveQuizCreateQuestionRequest $req): LiveQuizQuestionView
    {
        $this->assertActive($session);
        $this->requireValid($req->label === null || mb_strlen($req->label) <= 500, 'Question must be at most 500 characters');
        $this->requireValid($req->durationSeconds >= 5 && $req->durationSeconds <= 300, 'Duration must be 5-300 seconds');
        $this->requireValid(count($req->options) >= 2 && count($req->options) <= 10, 'A question needs 2-10 options');
        foreach ($req->options as $optionLabel) {
            $this->requireValid(
                is_string($optionLabel) && trim($optionLabel) !== '' && mb_strlen($optionLabel) <= 200,
                'Options must be 1-200 characters'
            );
        }

        $question = new LiveQuizQuestion(
            session: $session,
            label: $req->label,
            durationSeconds: $req->durationSeconds,
        );

        foreach (array_values($req->options) as $index => $optionLabel) {
            $question->getOptions()->add(new LiveQuizOption($question, (string) $optionLabel, $index));
        }

        $this->entityManager->persist($question);
        $this->entityManager->flush();

        return Mapper::mapOne($question, LiveQuizQuestionView::class);
    }

    public function pushQuestion(LiveQuizSession $session, LiveQuizQuestion $question): LiveQuizQuestionView
    {
        $this->assertBelongsTo($session, $question);
        $this->assertActive($session);

        // Pushing again would restart the timer on a question that already has answers.
        if ($question->getOpenedAt() !== null) {
            throw new ConflictHttpException('Question has already been pushed');
        }

        $openedAt = new DateTime();
        $question->setOpenedAt($openedAt);
        $this->entityManager->flush();

        $options = array_map(
            fn(LiveQuizOption $o) => [
                'id'    => $o->getId(),
                'label' => $o->getLabel(),
                'order' => $o->getOrder(),
            ],
            $question->getOptions()->toArray()
        );

        $this->sendData([
            'live-quiz-question' => 'true',
            'sessionId'          => $session->getId(),
            'questionId'         => $question->getId(),
            'label'              => $question->getLabel() ?? '',
            'durationSeconds'    => (string) $question->getDurationSeconds(),
            'openedAt'           => $openedAt->format(DateTime::ATOM),
            'options'            => json_encode(array_values($options), JSON_THROW_ON_ERROR),
        ]);

        return Mapper::mapOne($question, LiveQuizQuestionView::class);
    }

    public function submitResponse(
        LiveQuizSession $session,
        LiveQuizQuestion $question,
        LiveQuizRespondRequest $req,
        Attendee $attendee,
    ): void {
        $this->assertBelongsTo($session, $question);
        $this->assertActive($session);

        if (!$question->isOpen()) {
            throw new AccessDeniedHttpException('Question is not open');
        }

        $option = null;
        foreach ($question->getOptions() as $o) {
            if ($o->getId() === strtolower($req->optionId)) {
                $option = $o;
                break;
            }
        }

        if ($option === null) {
            throw new UnprocessableEntityHttpException('Option does not belong to this question');
        }

        $participant = $this->participantRepository->findBySessionAndAttendee($session, $attendee);

        if ($participant === null) {
            throw new AccessDeniedHttpException('Not a participant in this session');
        }

        $existing = $this->responseRepository->findOneBy([
            'question'    => $question,
            'participant' => $participant,
        ]);

        if ($existing !== null) {
            throw new ConflictHttpException('Already responded to this question');
        }

        $this->entityManager->persist(new LiveQuizResponse($question, $option, $participant));
        $this->flushUnique('Already responded to this question');
    }

    // Staff only (see LiveQuizVoter): participants never get to see results.
    public function getResults(LiveQuizSession $session, LiveQuizQuestion $question): LiveQuizResultView
    {
        $this->assertBelongsTo($session, $question);

        $counts = $this->responseRepository->countByQuestionGroupedByOption($question);
        $totalResponses = (int) array_sum(array_column($counts, 'count'));

        $result = new LiveQuizResultView();
        $result->questionId = $question->getId();
        $result->totalResponses = $totalResponses;
        $result->options = array_map(
            function (array $row) use ($totalResponses): LiveQuizOptionResultView {
                $optResult = new LiveQuizOptionResultView();
                $optResult->optionId = $row['option']->getId();
                $optResult->label = $row['option']->getLabel();
                $optResult->count = $row['count'];
                $optResult->percentage = $totalResponses > 0
                    ? round(($row['count'] / $totalResponses) * 100, 2)
                    : 0.0;
                return $optResult;
            },
            $counts
        );

        return $result;
    }

    public function getCurrentQuestion(LiveQuizSession $session): ?LiveQuizQuestion
    {
        if (!$session->isActive()) {
            return null;
        }

        return $this->questionRepository->findLatestPushed($session);
    }

    /** @return LiveQuizSessionView[] */
    public function getActiveSessions(): array
    {
        $sessions = $this->sessionRepository->findBy(['isActive' => true], ['createdAt' => 'DESC']);

        return array_map(fn(LiveQuizSession $s) => $this->toSessionView($s), $sessions);
    }

    public function deactivateSession(LiveQuizSession $session): void
    {
        if (!$session->isActive()) {
            return;
        }

        $session->setIsActive(false);
        $this->entityManager->flush();

        $this->sendData([
            'live-quiz-session-ended' => 'true',
            'sessionId'               => $session->getId(),
        ]);
    }

    /** @param array<non-empty-string, string> $data */
    private function sendData(array $data): void
    {
        try {
            $this->firebaseMessaging->send(CloudMessage::new()->toTopic('PCH')->withData($data));
        } catch (MessagingException|FirebaseException $e) {
            // Clients can still find out via "Reload Question".
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }

    private function toSessionView(LiveQuizSession $session): LiveQuizSessionView
    {
        /** @var LiveQuizSessionView $view */
        $view = Mapper::mapOne($session, LiveQuizSessionView::class);
        // Sessions from before plain-text storage only have a hash, which is useless to show.
        $view->password = LiveQuizSession::isHashedPassword($session->getPassword()) ? null : $session->getPassword();

        return $view;
    }

    // The route takes both ids, so without this a participant of one session could act on another's questions.
    private function assertBelongsTo(LiveQuizSession $session, LiveQuizQuestion $question): void
    {
        if ($question->getSessionId() !== $session->getId()) {
            throw new NotFoundHttpException('Question not found in this session');
        }
    }

    // symfony/validator isn't installed, so MapRequestPayload doesn't validate; these checks are the only guard.
    private function requireValid(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new UnprocessableEntityHttpException($message);
        }
    }

    private function assertActive(LiveQuizSession $session): void
    {
        if (!$session->isActive()) {
            throw new ConflictHttpException('Session has been closed');
        }
    }

    // The find-then-insert checks race with a double tap; the unique index is the real guard.
    private function flushUnique(string $conflictMessage): void
    {
        try {
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new ConflictHttpException($conflictMessage, $e);
        }
    }
}

<?php

namespace App\Controller;

use App\Application\Request\LiveQuizCreateQuestionRequest;
use App\Application\Request\LiveQuizCreateSessionRequest;
use App\Application\Request\LiveQuizJoinSessionRequest;
use App\Application\Request\LiveQuizRespondRequest;
use App\Application\Service\LiveQuizApplicationService;
use App\Application\View\LiveQuizQuestionView;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\LiveQuizQuestion;
use App\Domain\Entity\LiveQuizSession;
use App\Security\Voter\LiveQuizVoter;
use App\Util\SymfonyUtils\Mapper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LiveQuizController extends AbstractController
{
    public function __construct(
        private readonly LiveQuizApplicationService $liveQuizApplicationService,
    ) {
    }

    #[IsGranted(LiveQuizVoter::LIST_SESSIONS)]
    public function listSessions(): Response
    {
        return $this->json(
            $this->liveQuizApplicationService->getActiveSessions()
        );
    }

    #[IsGranted(LiveQuizVoter::CREATE_SESSION)]
    public function createSession(
        #[MapRequestPayload] LiveQuizCreateSessionRequest $req,
    ): Response {
        /** @var Attendee $attendee */
        $attendee = $this->getUser();

        return $this->json(
            $this->liveQuizApplicationService->createSession($req, $attendee),
            Response::HTTP_CREATED
        );
    }

    #[IsGranted(LiveQuizVoter::JOIN_SESSION)]
    public function joinSession(
        #[MapRequestPayload] LiveQuizJoinSessionRequest $req,
    ): Response {
        /** @var Attendee $attendee */
        $attendee = $this->getUser();

        return $this->json(
            $this->liveQuizApplicationService->joinSession($req, $attendee)
        );
    }

    #[IsGranted(LiveQuizVoter::JOIN_SESSION)]
    public function getJoinedSession(): Response
    {
        /** @var Attendee $attendee */
        $attendee = $this->getUser();

        return $this->json($this->liveQuizApplicationService->getJoinedSession($attendee));
    }

    #[IsGranted(LiveQuizVoter::MANAGE_QUESTION)]
    public function createQuestion(
        #[MapEntity(id: 'session')] LiveQuizSession $session,
        #[MapRequestPayload] LiveQuizCreateQuestionRequest $req,
    ): Response {
        return $this->json(
            $this->liveQuizApplicationService->createQuestion($session, $req),
            Response::HTTP_CREATED
        );
    }

    #[IsGranted(LiveQuizVoter::MANAGE_QUESTION)]
    public function pushQuestion(
        #[MapEntity(id: 'session')] LiveQuizSession $session,
        #[MapEntity(id: 'question')] LiveQuizQuestion $question,
    ): Response {
        return $this->json(
            $this->liveQuizApplicationService->pushQuestion($session, $question)
        );
    }

    #[IsGranted(LiveQuizVoter::VIEW_CURRENT)]
    public function getCurrentQuestion(
        #[MapEntity(id: 'session')] LiveQuizSession $session,
    ): Response {
        $question = $this->liveQuizApplicationService->getCurrentQuestion($session);

        if ($question === null) {
            return $this->json(null);
        }

        return $this->json(Mapper::mapOne($question, LiveQuizQuestionView::class));
    }

    #[IsGranted(LiveQuizVoter::SUBMIT_RESPONSE)]
    public function submitResponse(
        #[MapEntity(id: 'session')] LiveQuizSession $session,
        #[MapEntity(id: 'question')] LiveQuizQuestion $question,
        #[MapRequestPayload] LiveQuizRespondRequest $req,
    ): Response {
        /** @var Attendee $attendee */
        $attendee = $this->getUser();

        $this->liveQuizApplicationService->submitResponse($session, $question, $req, $attendee);

        return $this->json(['submitted' => true], Response::HTTP_CREATED);
    }

    #[IsGranted(LiveQuizVoter::VIEW_RESULTS)]
    public function getResults(
        #[MapEntity(id: 'session')] LiveQuizSession $session,
        #[MapEntity(id: 'question')] LiveQuizQuestion $question,
    ): Response {
        return $this->json(
            $this->liveQuizApplicationService->getResults($session, $question)
        );
    }

    #[IsGranted(LiveQuizVoter::DEACTIVATE_SESSION)]
    public function deactivateSession(
        #[MapEntity(id: 'session')] LiveQuizSession $session,
    ): Response {
        $this->liveQuizApplicationService->deactivateSession($session);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}

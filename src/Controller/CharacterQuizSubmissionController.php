<?php

namespace App\Controller;

use App\Application\Request\CharacterQuizSubmissionRequest;
use App\Application\Service\CharacterQuizSubmissionApplicationService;
use App\Application\View\CharacterQuizSubmissionView;
use App\DataAccessLayer\Repository\CharacterQuizSubmissionRepository;
use App\Domain\Entity\Attendee;
use App\Util\Exceptions\Exception\Common\OperationNotAllowedException;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class CharacterQuizSubmissionController extends AbstractController
{
    public function __construct(
        private readonly CharacterQuizSubmissionRepository $characterQuizSubmissionRepository,
        private readonly CharacterQuizSubmissionApplicationService $characterQuizSubmissionApplicationService
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Character Quiz Submission (last)',
        content: new OA\JsonContent(
            ref: new Model(
                     type: CharacterQuizSubmissionView::class
                 )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    public function getLastCharacterQuiz(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Attendee) {
            throw new OperationNotAllowedException('User is not an attendee');
        }

        $result = $this->characterQuizSubmissionRepository->lastForAttendee($user);
        if ($result === null) {
            throw new EntityNotFoundException('Character Quiz Submission not found');
        }

        return $this->json(
            Mapper::mapOne($result, CharacterQuizSubmissionView::class),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Submit Character Quiz',
        content: new OA\JsonContent(
            ref: new Model(
                     type: CharacterQuizSubmissionView::class
                 )
        )
    )]
    #[OA\RequestBody(
        description: 'CharacterQuiz Submission Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                     type: CharacterQuizSubmissionRequest::class
                 )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    public function submitCharacterQuiz(
        #[MapRequestPayload] CharacterQuizSubmissionRequest $characterQuizSubmissionRequest
    ): Response {
        $attendee = $this->getUser();
        if (!$attendee instanceof Attendee) {
            throw new OperationNotAllowedException('Only attendees can submit character quizzes');
        }

        return $this->json(
            Mapper::mapOne(
                $this->characterQuizSubmissionApplicationService->submitCharacterQuiz(
                    $attendee,
                    $characterQuizSubmissionRequest
                ),
                CharacterQuizSubmissionView::class
            ),
            Response::HTTP_CREATED
        );
    }
}
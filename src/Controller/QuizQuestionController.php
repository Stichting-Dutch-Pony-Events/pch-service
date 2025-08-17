<?php

namespace App\Controller;

use App\Application\Request\ChangeOrderRequest;
use App\Application\Request\QuizQuestionRequest;
use App\Application\Service\QuizQuestionApplicationService;
use App\Application\View\QuizQuestionView;
use App\DataAccessLayer\Repository\QuizQuestionRepository;
use App\Domain\Entity\QuizQuestion;
use App\Security\Voter\QuizQuestionVoter;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class QuizQuestionController extends AbstractController
{
    public function __construct(
        private QuizQuestionRepository         $quizQuestionRepository,
        private QuizQuestionApplicationService $quizQuestionApplicationService
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Character Quiz Questions (all)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: QuizQuestionView::class
                )
            )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    public function all(): Response
    {
        return $this->json(
            Mapper::mapMany($this->quizQuestionRepository->getOrdered(), QuizQuestionView::class),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Character Quiz Question',
        content: new OA\JsonContent(
            ref: new Model(
                type: QuizQuestionView::class
            )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    public function getQuizQuestion(
        #[MapEntity(id: 'question')] QuizQuestion $quizQuestion
    ): Response {
        return $this->json(
            Mapper::mapOne($quizQuestion, QuizQuestionView::class),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Create Quiz Question',
        content: new OA\JsonContent(
            ref: new Model(
                type: QuizQuestionView::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Bad Request',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Quiz Question Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: QuizQuestionRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    #[IsGranted(QuizQuestionVoter::CREATE_QUESTION)]
    public function createQuestion(
        #[MapRequestPayload] QuizQuestionRequest $quizQuestionRequest
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->quizQuestionApplicationService->createQuizQuestion($quizQuestionRequest),
                QuizQuestionView::class
            ),
            Response::HTTP_CREATED
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Update Quiz Question',
        content: new OA\JsonContent(
            ref: new Model(
                type: QuizQuestionView::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Invalid Input',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Quiz Question Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: QuizQuestionRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    #[IsGranted(QuizQuestionVoter::EDIT_QUESTION, subject: 'question')]
    public function updateQuestion(
        #[MapEntity(id: 'question')] QuizQuestion $question,
        #[MapRequestPayload] QuizQuestionRequest  $quizQuestionRequest,
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->quizQuestionApplicationService->updateQuizQuestion($question, $quizQuestionRequest),
                QuizQuestionView::class
            )
        );
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Change Quiz Question Order',
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Bad Request',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Change Order Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: ChangeOrderRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    #[IsGranted(QuizQuestionVoter::EDIT_QUESTION)]
    public function changeOrder(
        #[MapRequestPayload] ChangeOrderRequest $changeOrderRequest
    ): Response {
        $this->quizQuestionApplicationService->changeOrder($changeOrderRequest);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Quiz Question Deleted',
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Invalid Input',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    #[IsGranted(QuizQuestionVoter::DELETE_QUESTION, subject: 'question')]
    public function deleteQuestion(
        #[MapEntity(id: 'question')] QuizQuestion $question,
    ): Response {
        $this->quizQuestionApplicationService->deleteQuestion($question);
        return new Response("", Response::HTTP_NO_CONTENT);
    }
}
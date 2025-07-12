<?php

namespace App\Controller;

use App\Application\Request\ChangeOrderRequest;
use App\Application\Request\QuizAnswerRequest;
use App\Application\Service\QuizAnswerApplicationService;
use App\Application\View\QuizAnswerView;
use App\Domain\Entity\QuizAnswer;
use App\Domain\Entity\QuizQuestion;
use App\Security\Voter\QuizAnswerVoter;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class QuizAnswerController extends AbstractController
{
    public function __construct(
        private readonly QuizAnswerApplicationService $answerApplicationService
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Character Quiz Answers (by question)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: QuizAnswerView::class
                )
            )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    public function getAnswers(
        #[MapEntity(id: 'question')] QuizQuestion $quizQuestion
    ): Response {
        return $this->json(
            Mapper::mapMany($quizQuestion->getAnswers(), QuizAnswerView::class),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Create Quiz Answer',
        content: new OA\JsonContent(
            ref: new Model(
                type: QuizAnswerView::class
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
                type: QuizAnswerRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    #[IsGranted(QuizAnswerVoter::CREATE_ANSWER)]
    public function createAnswer(
        #[MapEntity(id: 'question')] QuizQuestion $quizQuestion,
        #[MapRequestPayload] QuizAnswerRequest    $quizQuestionRequest
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->answerApplicationService->createQuizAnswer($quizQuestion, $quizQuestionRequest),
                QuizAnswerView::class
            ),
            Response::HTTP_CREATED
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Update Quiz Answer',
        content: new OA\JsonContent(
            ref: new Model(
                type: QuizAnswerView::class
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
        description: 'Quiz Answer Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: QuizAnswerRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Character Quiz')]
    #[IsGranted(QuizAnswerVoter::EDIT_ANSWER, subject: 'answer')]
    public function updateAnswer(
        #[MapEntity(id: 'question')] QuizQuestion $question,
        #[MapEntity(id: 'answer')] QuizAnswer     $answer,
        #[MapRequestPayload] QuizAnswerRequest    $quizAnswerRequest,
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->answerApplicationService->updateQuizAnswer($question, $answer, $quizAnswerRequest),
                QuizAnswerView::class
            )
        );
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Change Quiz Answer Order',
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
    #[IsGranted(QuizAnswerVoter::EDIT_ANSWER)]
    public function changeOrder(
        #[MapEntity(id: 'question')] QuizQuestion $question,
        #[MapRequestPayload] ChangeOrderRequest   $changeOrderRequest
    ): Response {
        $this->answerApplicationService->changeOrder($question, $changeOrderRequest);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Quiz Answer Deleted',
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
    #[IsGranted(QuizAnswerVoter::DELETE_ANSWER, subject: 'answer')]
    public function deleteAnswer(
        #[MapEntity(id: 'question')] QuizQuestion $question,
        #[MapEntity(id: 'answer')] QuizAnswer     $answer,
    ): Response {
        $this->answerApplicationService->deleteQuizAnswer($question, $answer);
        return new Response("", Response::HTTP_NO_CONTENT);
    }
}
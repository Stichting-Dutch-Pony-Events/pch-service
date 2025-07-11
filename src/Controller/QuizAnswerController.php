<?php

namespace App\Controller;

use App\Application\View\QuizAnswerView;
use App\Domain\Entity\QuizQuestion;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class QuizAnswerController extends AbstractController
{
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
}
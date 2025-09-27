<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class QuizQuestionView
{
    use EntityViewTrait;

    public string $title;
    public string $question;
    public int $order;

    /** @var QuizAnswerView[] $answers */
    #[OA\Property(
        type: "array",
        items: new OA\Items(ref: new Model(type: QuizAnswerView::class))
    )]
    #[MapsMany(QuizAnswerView::class)]
    public array $answers;
}
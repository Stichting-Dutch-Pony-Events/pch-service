<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class QuizAnswerView
{
    use EntityViewTrait;

    public string $title;
    public string $answer;
    public int $order;

    /** @var QuizAnswerTeamWeightView[] */
    #[OA\Property(
        type: "array",
        items: new OA\Items(ref: new Model(type: QuizAnswerTeamWeightView::class))
    )]
    #[MapsMany(QuizAnswerTeamWeightView::class)]
    public array $quizAnswerTeamWeights;
}
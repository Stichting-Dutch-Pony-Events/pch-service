<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use JMS\Serializer\Annotation\Type;

class QuizAnswerView
{
    use EntityViewTrait;

    public string $title;
    public string $answer;
    public int $order;

    /** @var QuizAnswerTeamWeightView[] */
    #[Type('array<' . QuizAnswerTeamWeightView::class . '>')]
    #[MapsMany(QuizAnswerTeamWeightView::class)]
    public array $quizAnswerTeamWeights;
}
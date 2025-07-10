<?php

namespace App\Application\View;

use App\Util\SymfonyUtils\Attribute\MapsMany;
use JMS\Serializer\Annotation\Type;

class QuizAnswerDetailedView extends QuizAnswerView
{
    /** @var QuizAnswerTeamWeightView[] */
    #[Type('array<' . QuizAnswerTeamWeightView::class . '>')]
    #[MapsMany(QuizAnswerTeamWeightView::class)]
    public array $quizAnswerTeamWeights;
}
<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use JMS\Serializer\Annotation\Type;

class QuizQuestionDetailedView
{
    use EntityViewTrait;

    public string $question;
    public int $order;

    /** @var QuizAnswerDetailedView[] $answers */
    #[Type('array<' . QuizAnswerDetailedView::class . '>')]
    #[MapsMany(QuizAnswerDetailedView::class)]
    public array $answers;
}
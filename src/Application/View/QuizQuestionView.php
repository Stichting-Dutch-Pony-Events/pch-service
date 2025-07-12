<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use JMS\Serializer\Annotation\Type;

class QuizQuestionView
{
    use EntityViewTrait;

    public string $title;
    public string $question;
    public int $order;

    /** @var QuizAnswerView[] $answers */
    #[Type('array<' . QuizAnswerView::class . '>')]
    #[MapsMany(QuizAnswerView::class)]
    public array $answers;
}
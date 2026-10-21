<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use DateTime;

class LiveQuizQuestionView
{
    use EntityViewTrait;

    public string $sessionId;
    public ?string $label;
    public int $durationSeconds;
    public ?DateTime $openedAt;
    public bool $isOpen;
    public bool $isClosed;

    /** @var LiveQuizOptionView[] */
    #[MapsMany(LiveQuizOptionView::class)]
    public array $options;
}

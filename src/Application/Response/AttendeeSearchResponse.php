<?php

namespace App\Application\Response;

use App\Application\View\AttendeeSimpleView;
use App\Domain\Entity\Attendee;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use App\Util\SymfonyUtils\Exception\WrongTypeException;
use App\Util\SymfonyUtils\Mapper;
use JMS\Serializer\Annotation\Type;
use ReflectionException;

class AttendeeSearchResponse extends BaseSearchResponse
{
    /** @var AttendeeSimpleView[] $items */
    #[Type('array<' . AttendeeSimpleView::class . '>')]
    #[MapsMany(AttendeeSimpleView::class)]
    public array $items = [];

    /**
     * AttendeeSearchResponse constructor.
     *
     * @param Attendee[] $items
     * @param int $total
     * @param int $page
     * @param int $itemsPerPage
     * @throws ReflectionException|WrongTypeException
     */
    public function __construct(
        array $items = [],
        int   $total = 0,
        int   $page = 1,
        int   $itemsPerPage = 10
    ) {
        $this->items = Mapper::mapMany($items, AttendeeSimpleView::class);
        $this->total = $total;
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
    }
}
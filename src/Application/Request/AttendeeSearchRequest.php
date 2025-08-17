<?php

namespace App\Application\Request;

use App\Security\Enum\RoleEnum;

class AttendeeSearchRequest
{
    public function __construct(
        public ?string   $query,
        public ?string   $productId,
        public ?RoleEnum $role,
        public int       $page,
        public int       $itemsPerPage,
        public ?string   $sortBy
    ) {
    }

    /**
     * @return SortItem[]
     */
    public function getSorts(): array
    {
        if (!$this->sortBy) {
            return [];
        }

        $sortItems = [];
        $sorts = explode(',', $this->sortBy);
        foreach ($sorts as $sort) {
            if (!str_contains($sort, ':')) {
                $sortItems[] = new SortItem($sort, 'asc');
                continue;
            }

            [$key, $direction] = explode(':', $sort);
            $sortItems[] = new SortItem($key, empty($direction) ? 'asc' : $direction);
        }

        return $sortItems;
    }
}
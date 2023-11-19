<?php

namespace App\DataAccessLayer\Pretix\Repositories;

use App\DataAccessLayer\Pretix\Views\CheckInList;

class CheckInListRepository extends PretixBaseRepository
{
    /**
     * @return CheckInList[]
     */
    public function getCheckinLists(): array {
        $checkinLists = $this->retrieveAll('checkinlists');

        $checkinListObjs = [];
        foreach ($checkinLists as $checkinList) {
            $checkinListObjs[] = new CheckInList($checkinList);
        }

        return $checkinListObjs;
    }

    public function getCheckInListById(string $id): CheckInList
    {
        $uri = "checkinlists/".$id;
        return new CheckInList(json_decode($this->getClient()->get($uri)->getBody()));
    }
}

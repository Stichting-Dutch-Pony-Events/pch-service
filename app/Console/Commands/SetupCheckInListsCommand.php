<?php

namespace App\Console\Commands;

use App\Application\Service\CheckInListApplicationService;
use App\DataAccessLayer\Pretix\Repositories\CheckInListRepository;
use App\DataAccessLayer\Pretix\Views\CheckInList as PretixCheckInList;
use App\Domain\Enum\CheckInListTypeEnum;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetupCheckInListsCommand extends Command
{
    /** @var PretixCheckInList[] */
    private array $pretixCheckInLists = [];

    public function __construct(
        private readonly CheckInListRepository $checkInListRepository,
        private readonly CheckInListApplicationService $checkInListApplicationService
    ) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:check-in-lists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup CheckIn Lists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('Do you want to setup the Check-In lists? This will delete any previous data')) {
            $this->line('Retrieving Check-In lists from '.config('pretix.url'));
            $this->pretixCheckInLists = $this->checkInListRepository->getCheckinLists();
            $this->info('Found '.count($this->pretixCheckInLists).' check-in lists.');

            foreach ($this->pretixCheckInLists as $pretixCheckInList) {
                $this->createCheckInList($pretixCheckInList);
            }
        }
    }

    public function createCheckInList(PretixCheckInList $checkInList)
    {
        $this->line('Setting up Check-In List '.$checkInList->getName());

        $type = CheckInListTypeEnum::from(strtoupper($this->choice('What is the type of this CheckInList',
            ['Ticket', 'Merch', 'Special'], 0)));


        $startTime = $this->askDate('Starttime for this check-in list');
        $endTime = $this->askDate('Endtime for this check-in list');

        $this->checkInListApplicationService->createCheckInList($checkInList, $type, $startTime, $endTime);
        $this->info('Check-In list created!');
    }

    public function askDate(string $question, $maxTries = 5): Carbon
    {
        for ($i = 0; $i < $maxTries; $i++) {
            $date = $this->ask($question.' (YYYY-MM-DD HH:ii:ss)');
            if(preg_match('/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])(?:( [0-2][0-9]):([0-5][0-9]):([0-5][0-9]))$/', $date)) {
                return Carbon::parse($date);
            } else {
                $i++;
                if($i < $maxTries) {
                    $this->error('Date in wrong format :c try again');
                } else {
                    throw new \Exception("Tried too many times");
                }
            }
        }
    }
}

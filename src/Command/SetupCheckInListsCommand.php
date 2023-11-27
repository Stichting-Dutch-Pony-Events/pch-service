<?php

namespace App\Command;

use App\Application\Request\CheckInListRequest;
use App\Application\Service\CheckInListApplicationService;
use App\DataAccessLayer\Pretix\Repositories\CheckInListRepository as PretixCheckInListRepository;
use App\DataAccessLayer\Repository\CheckInListRepository;
use App\Domain\Enum\CheckInListType;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'setup:check-in-lists')]
class SetupCheckInListsCommand extends Command
{
    public function __construct(
        private readonly PretixCheckInListRepository   $pretixCheckInListRepository,
        private readonly CheckInListRepository         $checkInListRepository,
        private readonly EntityManagerInterface        $entityManager,
        private readonly CheckInListApplicationService $checkInListApplicationService
    ) {
        parent::__construct();
    }

    protected
    function execute(
        InputInterface  $input,
        OutputInterface $output
    ) {
        $helper = $this->getHelper('question');

        $output->writeln([
            '<info>Check-in list setup</info>',
            '============',
            'This command will fetch the check-in lists from pretix for your configured event and will guide you through setting them up.',
            '<comment>This will delete all previous check-in list config!</comment>',
            ''
        ]);

        $confirmationQuestion = new ConfirmationQuestion(
            '<question>Do you want to continue? (yes/no):</question> ',
            false
        );

        if (!$helper->ask($input, $output, $confirmationQuestion)) {
            $output->writeln('<error>Aborted!</error>');
            return Command::INVALID;
        }

        $this->deleteCurrentCheckInLists();

        $pretixCheckInLists = $this->pretixCheckInListRepository->getCheckinLists();

        $output->writeln([
            '',
            '<info>' . count($pretixCheckInLists) . ' Check-in lists fetched from pretix</info>',
            ''
        ]);

        foreach ($pretixCheckInLists as $pretixCheckInList) {
            $output->writeln([
                '<info>Check-in list: ' . $pretixCheckInList->getName() . '</info>',
                '============',
                ''
            ]);

            $checkInListTypes        = array_map(function (\UnitEnum $case) {
                return $case->value;
            }, CheckInListType::cases());
            $checkInListTypeQuestion = new ChoiceQuestion(
                '<question>What type of check-in list is this? (default: <comment>' . $checkInListTypes[0] . '</comment>):</question> ',
                $checkInListTypes,
                0
            );

            $checkInListType = CheckInListType::from($helper->ask($input, $output, $checkInListTypeQuestion));
            $startTime       = $this->getDate(
                $input,
                $output,
                '<question>What is the start time of this check-in list? (YYYY-MM-DD HH:MM:SS):</question> '
            );
            $endTime         = $this->getDate(
                $input,
                $output,
                '<question>What is the end time of this check-in list? (YYYY-MM-DD HH:MM:SS):</question> '
            );

            $checkInListRequest = new CheckInListRequest(
                name: $pretixCheckInList->getName(),
                pretixId: $pretixCheckInList->getId(),
                startTime: $startTime,
                endTime: $endTime,
                type: $checkInListType,
                pretixProductIds: $pretixCheckInList->getProductIds()
            );

            $checkInList = $this->checkInListApplicationService->createCheckInList($checkInListRequest);
            $output->writeln([
                '',
                '<info>Check-in list created with id: ' . $checkInList->getId() . '</info>',
                ''
            ]);
        }

        return Command::SUCCESS;
    }

    private
    function getDate(
        InputInterface  $input,
        OutputInterface $output,
        string          $question,
        int             $maxTries = 5
    ): ?DateTime {
        $helper   = $this->getHelper('question');
        $question = new Question($question, null);
        $question->setValidator(function ($answer) {
            if (preg_match(
                    '/^(((\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12][0-9]|3[01]))|((\d{4})(-)(0[469]|1‌​1)(-)([0][1-9]|[12][0-9]|30))|((\d{4})(-)(02)(-)(0[1-9]|1[0-9]|2[0-8]))|(([02468]‌​[048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|(([0-9][0-9][0][48])(-)(0‌​2)(-)(29))|(([0-9][0-9][2468][048])(-)(02)(-)(29))|(([0-9][0-9][13579][26])(-)(02‌​)(-)(29)))(\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))$/',
                    $answer
                ) === 1) {
                return Carbon::parse($answer);
            } else {
                throw new \RuntimeException(
                    'Please enter a valid date in the format YYYY-MM-DD HH:MM:SS'
                );
            }
        });
        $question->setMaxAttempts($maxTries);

        $date = $helper->ask($input, $output, $question);

        if ($date instanceof DateTime) {
            return $date;
        }

        return null;
    }

    private
    function deleteCurrentCheckInLists(): void
    {
        $checkInLists = $this->checkInListRepository->findAll();

        foreach ($checkInLists as $checkInList) {
            $this->entityManager->remove($checkInList);
        }
        $this->entityManager->flush();
    }
}

<?php

namespace App\Command;

use App\Application\Request\DispatchPrintJobRequest;
use App\Application\Service\PrintJobApplicationService;
use App\DataAccessLayer\Repository\AttendeeRepository;
use App\Domain\Entity\Attendee;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(name: 'print:badges', description: 'Print badges')]
class PrintBadgesCommand extends Command
{
    public function __construct(
        private readonly AttendeeRepository         $attendeeRepository,
        private readonly PrintJobApplicationService $printJobApplicationService,
        ?string                                     $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $output->writeln(
            [
                '<info>Print Badges</info>',
                '============',
                'This command will create print jobs for all attendees who do not already have a print job assigned.',
                ''
            ]
        );

        $confirmationQuestion = new ConfirmationQuestion(
            '<question>Do you want to continue? (yes/no):</question> ',
            false
        );

        if (!$helper->ask($input, $output, $confirmationQuestion)) {
            $output->writeln('<error>Aborted!</error>');
            return Command::INVALID;
        }

        $badgeTypeQuestion = new ChoiceQuestion(
            '<question>Which badges do you want to print?</question>',
            ['All', 'Regular', 'Backstage'],
            0
        );

        $badgeType = $helper->ask($input, $output, $badgeTypeQuestion);

        $includeRegular = true;
        $includeBackstage = true;

        switch ($badgeType) {
            case 'Regular':
                $includeBackstage = false;
                break;
            case 'Backstage':
                $includeRegular = false;
                break;
        }

        $attendees = $this->attendeeRepository->getAttendeesWithoutPrintJobs($includeRegular, $includeBackstage);

        $output->writeln('<info>Found ' . count($attendees) . ' Attendees without Printed Badges</info>');

        $output->writeln('<comment>Creating PrintJobs</comment>');
        $progressBar = new ProgressBar($output, count($attendees));

        foreach ($attendees as $attendee) {
            $this->createPrintJob($attendee);
            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln('');
        $output->writeln('<info>Created ' . count($attendees) . ' PrintJobs</info>');

        return Command::SUCCESS;
    }

    private function createPrintJob(Attendee $attendee): void
    {
        $this->printJobApplicationService->createPrintJob(new DispatchPrintJobRequest($attendee->getMiniIdentifier()));
    }
}
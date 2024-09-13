<?php

namespace App\Command;

use App\Application\Service\AttendeeApplicationService;
use App\DataAccessLayer\Pretix\Repositories\OrderRepository;
use App\DataAccessLayer\Pretix\Views\Order;
use App\DataAccessLayer\Repository\ProductRepository;
use App\Domain\Entity\Product;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(name: 'setup:attendees')]
class ImportAttendees extends Command
{
    public function __construct(
        private readonly OrderRepository            $orderRepository,
        private readonly AttendeeApplicationService $attendeeApplicationService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $output->writeln([
            '<info>Attendee Import</info>',
            '============',
            'This command will fetch all the Attendees from the backend.',
            '<comment>This will delete all previous products config!</comment>',
            '<comment>Make sure you ran the \'setup:check-in-lists\' and \'setup:products\' command first!</comment>',
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

        $output->writeln('<comment>Retrieving Order from Pretix!</comment>');
        $orders = $this->orderRepository->getOrders();
        $output->writeln('<info>Retrieved ' . count($orders) . ' Order from Pretix!</info>');

        $output->writeln('<comment>Importing Orders</comment>');
        $progressBar = new ProgressBar($output, count($orders));
        $attendeeCount = 0;

        foreach ($orders as $order) {
            $attendeeCount += $this->importOrder($order);
            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln('');
        $output->writeln('<info>Imported '.$attendeeCount.' Attendees</info>');

        return Command::SUCCESS;
    }

    private function importOrder(Order $order): int
    {
        $count = 0;

        foreach ($order->positions as $position) {
            try {
                $this->attendeeApplicationService->createAttendeeFromOrderPosition($position, $order);
                $count++;
            } catch (EntityNotFoundException $e) {
                continue;
            } catch (Exception $e) {
                var_dump($e->getMessage());
                continue;
            }
        }

        return $count;
    }
}

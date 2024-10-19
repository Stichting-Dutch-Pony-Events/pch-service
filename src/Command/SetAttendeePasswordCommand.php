<?php

namespace App\Command;

use App\DataAccessLayer\Repository\AttendeeRepository;
use App\Domain\Entity\Attendee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand("attendee:set-password")]
class SetAttendeePasswordCommand extends Command
{
    public function __construct(
        private AttendeeRepository                   $attendeeRepository,
        private EntityManagerInterface               $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        ?string                                      $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '<info>Create an api key</info>',
            '============',
            ''
        ]);

        $helper = $this->getHelper('question');

        $userId = $helper->ask(
            $input,
            $output,
            new Question("<question>What is an identifier of the user?</question> ")
        );

        $attendee = $this->attendeeRepository->loadUserByIdentifier($userId);

        if (!$attendee instanceof Attendee) {
            return Command::FAILURE;
        }

        $userPassword = $helper->ask(
            $input,
            $output,
            new Question("<question>New Pin?</question> ")
        );

        $attendee->setPassword($this->userPasswordHasher->hashPassword($attendee, $userPassword));

        $this->entityManager->flush();

        $output->writeln("<info>Attendee Pin Set:</info> ");

        return Command::SUCCESS;
    }
}
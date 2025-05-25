<?php

namespace App\Command;

use App\Domain\Service\TeamDomainService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'create:team')]
class CreateTeamCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TeamDomainService      $teamDomainService,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface  $input,
        OutputInterface $output
    ): int {
        $helper = $this->getHelper('question');

        $output->writeln([
            '<info>Create a team</info>',
            '============',
            ''
        ]);

        $name = $helper->ask($input, $output, new Question("What is the name of the team?", null));
        $description = $helper->ask($input, $output, new Question("What is the description of the team?"));
        $identifier = $helper->ask($input, $output, new Question("What is the ID of the team?"));

        $team = $this->teamDomainService->createTeam($name, $description, $identifier);

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        $output->writeln([
            '',
            '<info>Team created with id: ' . $team->getId() . '</info>',
            ''
        ]);

        return Command::SUCCESS;
    }
}
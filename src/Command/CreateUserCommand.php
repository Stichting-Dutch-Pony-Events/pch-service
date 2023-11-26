<?php

namespace App\Command;

use App\Application\Request\UserRequest;
use App\Application\Service\UserApplicationService;
use App\Domain\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserApplicationService $userApplicationService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '<info>User Creator</info>',
            '============',
            '<comment>Please enter the details of the new user below:</comment>',
            ''
        ]);

        $helper = $this->getHelper('question');

        $nameQuestion = new Question('<question>Name:</question> ');
        $name         = $helper->ask($input, $output, $nameQuestion);

        $usernameQuestion = new Question('<question>Username:</question> ');
        $username         = $helper->ask($input, $output, $usernameQuestion);

        $password          = '';
        $passwordConfirmed = false;

        while (!$passwordConfirmed) {
            $passwordQuestion = new Question('<question>Password:</question> ');
            $passwordQuestion->setHidden(true);
            $passwordQuestion->setHiddenFallback(false);

            $passwordConfirmQuestion = new Question('<question>Confirm Password:</question> ');
            $passwordConfirmQuestion->setHidden(true);
            $passwordConfirmQuestion->setHiddenFallback(false);

            $password             = $helper->ask($input, $output, $passwordQuestion);
            $passwordConfirmation = $helper->ask($input, $output, $passwordConfirmQuestion);

            if ($password === $passwordConfirmation) {
                $passwordConfirmed = true;
            } else {
                $output->writeln('<error>Passwords do not match! Try again!</error>');
            }
        }

        $roleQuestion = new ChoiceQuestion('<question>Role:</question> ', ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
        $role = $helper->ask($input, $output, $roleQuestion);

        $userRequest = new UserRequest(
            name: $name,
            username: $username,
            password: $password,
            roles: [$role]
        );

        $user = $this->userApplicationService->createUser($userRequest);

        $output->writeln('<info>User created successfully!</info>');

        return Command::SUCCESS;
    }
}
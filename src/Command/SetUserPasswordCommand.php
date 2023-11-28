<?php

namespace App\Command;

use App\Application\Request\UserRequest;
use App\Application\Service\UserApplicationService;
use App\DataAccessLayer\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand('app:set-user-password')]
class SetUserPasswordCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserApplicationService $userApplicationService
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '<info>User Password Reset</info>',
            '============',
            '<comment>Please enter the details of the user and the new password below:</comment>',
            ''
        ]);

        $helper = $this->getHelper('question');
        $usernameQuestion = new Question('<question>Username:</question> ');
        $usernameQuestion->setValidator(function ($answer) {
            $user = $this->userRepository->findOneBy(['username' => $answer]);
            if (!$user) {
                throw new \RuntimeException('User not found!');
            }
            return $user;
        });
        $usernameQuestion->setMaxAttempts(5);

        $user = $helper->ask($input, $output, $usernameQuestion);

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

        $this->userApplicationService->updateUser($user, new UserRequest(
            name: $user->getName(),
            username: $user->getUsername(),
            password: $password,
            roles: $user->getRoles()
        ));

        return Command::SUCCESS;
    }
}

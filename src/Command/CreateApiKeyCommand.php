<?php

namespace App\Command;

use App\DataAccessLayer\Repository\ApiKeyRepository;
use App\DataAccessLayer\Repository\AttendeeRepository;
use App\Domain\Entity\ApiKey;
use App\Domain\Entity\Attendee;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[AsCommand("create:api-key")]
class CreateApiKeyCommand extends Command
{
    public function __construct(
        private AttendeeRepository          $attendeeRepository,
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface      $entityManager,
        ?string                             $name = null
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
            new Question("<question>What is the pin of the user?</question> ")
        );

        if (!$this->hasher->isPasswordValid($attendee, $userPassword)) {
            return Command::FAILURE;
        }

        $apiKey = new ApiKey(key: $this->generateRandomString(128), attendee: $attendee);

        $this->entityManager->persist($apiKey);
        $this->entityManager->flush();

        $output->writeln("<info>API key created:</info> ");
        $output->writeln($apiKey->getKey());

        return Command::SUCCESS;
    }

    function generateRandomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
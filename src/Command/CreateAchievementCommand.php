<?php

namespace App\Command;

use App\DataAccessLayer\Repository\AchievementRepository;
use App\Domain\Entity\Achievement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand("create:achievement")]
class CreateAchievementCommand extends Command
{
    public function __construct(
        private AchievementRepository  $achievementRepository,
        private EntityManagerInterface $entityManager,
        ?string                        $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '<info>Create a team</info>',
            '============',
            ''
        ]);

        $helper = $this->getHelper('question');

        $name = $helper->ask($input, $output, new Question("<question>What is the name of the achievement?</question> "));
        $description = $helper->ask($input, $output, new Question("<question>What is the description of the achievement?</question> "));
        $pointValue = (int)$helper->ask($input, $output, new Question("<question>What is the point value?</question> "), 1);
        $isEveningActivity = $helper->ask(
            $input,
            $output,
            new ConfirmationQuestion("<question>Is this an Saturday Evening Activity? (yes/no):</question> ", false)
        );
        $unlockCode = $helper->ask($input, $output, new Question("<question>What is the unlock code?</question> ", null));
        $identifier = $helper->ask($input, $output, new Question("<question>What is the ID of the achievement?</question> "));

        $achievement = $this->achievementRepository->findOneBy(['identifier' => $identifier]);


        if ($achievement instanceof Achievement) {
            $achievement->setName($name)
                ->setDescription($description)
                ->setUnlockCode($unlockCode)
                ->setPointValue($pointValue)
                ->setEveningActivity($isEveningActivity);
        } else {
            $achievement = new Achievement(
                name: $name,
                description: $description,
                identifier: $identifier,
                pointValue: $pointValue,
                unlockCode: $unlockCode,
                eveningActivity: $isEveningActivity
            );

            $this->entityManager->persist($achievement);
        }

        $this->entityManager->flush();

        $output->writeln([
            '',
            '<info>Achievement created with id: ' . $achievement->getId() . '</info>',
            ''
        ]);

        return Command::SUCCESS;
    }
}
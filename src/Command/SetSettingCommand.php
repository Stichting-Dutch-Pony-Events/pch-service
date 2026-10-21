<?php

namespace App\Command;

use App\Application\Request\SettingRequest;
use App\Application\Service\SettingApplicationService;
use App\DataAccessLayer\Repository\SettingRepository;
use App\Domain\Entity\Setting;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'setting:set', description: 'Create or update a setting')]
class SetSettingCommand extends Command
{
    /**
     * @param SettingRepository<Setting> $settingRepository
     */
    public function __construct(
        private readonly SettingRepository         $settingRepository,
        private readonly SettingApplicationService $settingApplicationService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the setting')
            ->addArgument('value', InputArgument::OPTIONAL, 'The new value of the setting');
    }

    protected function execute(
        InputInterface  $input,
        OutputInterface $output
    ): int {
        $helper = $this->getHelper('question');

        if (!$helper instanceof QuestionHelper) {
            return Command::FAILURE;
        }

        $output->writeln([
            '<info>Set a setting</info>',
            '============',
            ''
        ]);

        $name = $input->getArgument('name');

        if ($name === null) {
            $settingNames = array_map(
                static fn (Setting $setting) => $setting->getName(),
                $this->settingRepository->findAll()
            );

            $question = new Question("<question>What is the name of the setting?</question> ");
            $question->setAutocompleterValues($settingNames);

            $name = $helper->ask($input, $output, $question);
        }

        if (empty($name)) {
            $output->writeln('<error>A setting name is required</error>');

            return Command::FAILURE;
        }

        $value = $input->getArgument('value');

        if ($value === null) {
            $currentSetting = $this->settingRepository->findOneBy(['name' => $name]);
            $currentValue = $currentSetting instanceof Setting ? $currentSetting->getValue() : null;

            if ($currentValue !== null) {
                $output->writeln("Current value: <comment>$currentValue</comment>");
            }

            $value = $helper->ask(
                $input,
                $output,
                new Question("<question>What is the new value?</question> ", $currentValue)
            );
        }

        $setting = $this->settingApplicationService->setSetting(new SettingRequest($name, (string) $value));

        $output->writeln([
            '',
            '<info>Setting ' . $setting->getName() . ' set to: ' . $setting->getValue() . '</info>',
            ''
        ]);

        return Command::SUCCESS;
    }
}

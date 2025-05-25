<?php

namespace App\Command;

use App\DataAccessLayer\Repository\AchievementRepository;
use App\DataAccessLayer\Repository\AttendeeAchievementRepository;
use App\DataAccessLayer\Repository\AttendeeRepository;
use App\DataAccessLayer\Repository\TeamRepository;
use App\Domain\Entity\Achievement;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\Team;
use Carbon\Carbon;
use DateTime;
use Google\Type\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand("generate:stats")]
class GenerateStatsCommand extends Command
{
    private int $currentRow = 1;

    private Spreadsheet $spreadsheet;

    private Worksheet $worksheet;

    public function __construct(
        private AchievementRepository $achievementRepository,
        private AttendeeRepository    $attendeeRepository,
        private TeamRepository        $teamRepository,
        private Filesystem            $filesystem,
        ?string                       $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '<info>Generating Stats</info>',
            '============',
            ''
        ]);

        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();

        $currentColumn = $this->generateAchievementStats($output);
        $currentColumn += 2;

        $this->generateTopAchieversStats($currentColumn, $input, $output);

        $writer = new Xlsx($this->spreadsheet);
        $path = __DIR__ . '/../../var/stats';
        $this->filesystem->mkdir($path);
        $output->writeln($path);
        $writer->save($path . '/stats.xlsx');

        return Command::SUCCESS;
    }

    protected function generateTopAchieversStats(
        int             $currentColumn,
        InputInterface  $input,
        OutputInterface $output
    ): int {
        $this->currentRow = 1;

        $attendeeColumn = $this->columnLetter($currentColumn);
        $amountColumn = $this->columnLetter($currentColumn + 1);
        $timeCompletedColumn = $this->columnLetter($currentColumn + 2);

        $this->worksheet->setCellValue($attendeeColumn . $this->currentRow, "Nickname");
        $this->worksheet->setCellValue($amountColumn . $this->currentRow, "Amount");
        $this->worksheet->setCellValue($timeCompletedColumn . $this->currentRow, "Time Completed");

        $startDate = $this->getDate($input, $output, "Start time for the achievements (UTC): ");

        $output->writeln('');

        $attendees = $this->attendeeRepository->findAll();

        $output->writeln('<comment>Tallying Achievements for ' . count($attendees) . ' attendees</comment>');

        $progressBar = new ProgressBar($output, count($attendees));

        usort($attendees, static function (Attendee $a, Attendee $b) {
            // Sort by achievementCount
            $sortVal = $b->getAchievements()->count() <=> $a->getAchievements()->count();

            if ($sortVal !== 0) {
                return $sortVal;
            }

            $aLast = new DateTime("@0");
            $bLast = new DateTime("@0");

            foreach ($a->getAchievements() as $aAchievement) {
                if ($aAchievement->getCreatedAt() > $aLast) {
                    $aLast = $aAchievement->getCreatedAt();
                }
            }

            foreach ($b->getAchievements() as $bAchievement) {
                if ($bAchievement->getCreatedAt() > $bLast) {
                    $bLast = $bAchievement->getCreatedAt();
                }
            }

            return $aLast <=> $bLast;
        });

        foreach ($attendees as $attendee) {
            $progressBar->advance();
            $this->currentRow++;

            $this->worksheet->setCellValue(
                $attendeeColumn . $this->currentRow,
                empty($attendee->getNickname()) ? 'Unknown Pony' : $attendee->getNickname()
            );
            $this->worksheet->setCellValue($amountColumn . $this->currentRow, $attendee->getAchievements()->count());

            $lastAchievement = new DateTime("@0");
            foreach ($attendee->getAchievements() as $attendeeAchievement) {
                if ($attendeeAchievement->getCreatedAt() > $lastAchievement) {
                    $lastAchievement = $attendeeAchievement->getCreatedAt();
                }
            }

            $diff = $lastAchievement->getTimestamp() - $startDate?->getTimestamp() ?? 0;

            $this->worksheet->setCellValue($timeCompletedColumn . $this->currentRow, $this->seconds2human($diff));
        }

        $progressBar->finish();

        return 0;
    }

    protected function generateAchievementStats(OutputInterface $output): int
    {
        $teams = $this->teamRepository->findAll();

        $teamColumns = [
            'ALL' => 'B',
        ];

        $this->worksheet->setCellValue('A' . $this->currentRow, 'Achievement');
        $this->worksheet->setCellValue('B' . $this->currentRow, 'All');
        $currentColumn = 2;

        /** @var Team $team */
        foreach ($teams as $team) {
            $currentColumn++;
            $columnName = $this->columnLetter($currentColumn);
            $this->worksheet->setCellValue($columnName . $this->currentRow, $team->getName());
            $teamColumns[$team->getIdentifier()] = $columnName;
        }

        $achievements = $this->achievementRepository->findAll();
        $achievementData = [];

        /** @var Achievement $achievement */
        foreach ($achievements as $achievement) {
            $this->currentRow++;
            $this->worksheet->setCellValue('A' . $this->currentRow, $achievement->getName());

            $achievementData[$achievement->getIdentifier()] = [
                'row' => $this->currentRow,
                'ALL' => 0,
            ];

            foreach ($teams as $team) {
                $achievementData[$achievement->getIdentifier()][$team->getIdentifier()] = 0;
            }
        }

        $attendees = $this->attendeeRepository->findAll();

        $output->writeln('<comment>Tallying Achievements for ' . count($attendees) . ' attendees</comment>');

        $progressBar = new ProgressBar($output, count($attendees));

        /** @var Attendee $attendee */
        foreach ($attendees as $attendee) {
            $progressBar->advance();

            foreach ($attendee->getAchievements() as $attendeeAchievement) {
                $achievement = $attendeeAchievement->getAchievement();
                if (array_key_exists($achievement->getIdentifier(), $achievementData)) {
                    $achievementData[$achievement->getIdentifier()]['ALL']++;
                    $attendeeTeam = $attendee->getTeam();
                    if ($attendeeTeam) {
                        $achievementData[$achievement->getIdentifier()][$attendeeTeam->getIdentifier()]++;
                    }
                }
            }
        }
        $progressBar->finish();

        foreach ($achievementData as $achievementCounts) {
            foreach ($achievementCounts as $achievementCountKey => $achievementCount) {
                if ($achievementCountKey === 'row') {
                    continue;
                }

                if (array_key_exists($achievementCountKey, $teamColumns)) {
                    $this->worksheet->setCellValue(
                        $teamColumns[$achievementCountKey] . $achievementCounts['row'],
                        $achievementCount
                    );
                }
            }
        }

        $output->writeln('');

        return count($teams) + 2;
    }

    private function columnLetter($c): string
    {
        $c = (int)$c;
        if ($c <= 0) {
            return '';
        }

        $letter = '';

        while ($c !== 0) {
            $p = ($c - 1) % 26;
            $c = (int)(($c - $p) / 26);
            $letter = chr(65 + $p) . $letter;
        }

        return $letter;
    }

    private function getDate(
        InputInterface  $input,
        OutputInterface $output,
        string          $questionText,
        int             $maxTries = 5
    ): ?DateTime {
        $helper = $this->getHelper('question');
        $question = new Question($questionText, null);
        $question->setValidator(function ($answer) {
            if (preg_match(
                    '/^(((\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12]\d|3[01]))|((\d{4})(-)(0[469]|1‌​1)(-)(0[1-9]|[12]\d|30))|((\d{4})(-)(02)(-)(0[1-9]|1\d|2[0-8]))|(([02468]‌​[048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|((\d\d0[48])(-)(0‌​2)(-)(29))|((\d\d[2468][048])(-)(02)(-)(29))|((\d\d[13579][26])(-)(02‌​)(-)(29)))(\s([0-1]\d|2[0-4]):([0-5]\d):([0-5]\d))$/u',
                    $answer
                ) === 1) {
                return Carbon::parse($answer);
            }

            throw new \RuntimeException(
                'Please enter a valid date in the format YYYY-MM-DD HH:MM:SS'
            );
        });
        $question->setMaxAttempts($maxTries);

        $date = $helper->ask($input, $output, $question);

        if ($date instanceof DateTime) {
            return $date;
        }

        return null;
    }

    private function seconds2human($ss): string
    {
        $s = $ss % 60;
        $m = floor(($ss % 3600) / 60);
        $h = floor(($ss) / 3600);

        return "$h hours, $m minutes, $s seconds";
    }
}
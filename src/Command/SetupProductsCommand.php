<?php

namespace App\Command;

use App\Application\Request\ProductRequest;
use App\Application\Service\ProductApplicationService;
use App\DataAccessLayer\Pretix\Repositories\ItemRepository;
use App\DataAccessLayer\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand('setup:products')]
class SetupProductsCommand extends Command
{
    public function __construct(
        private readonly ItemRepository            $pretixItemRepository,
        private readonly EntityManagerInterface    $entityManager,
        private readonly ProductRepository         $productRepository,
        private readonly ProductApplicationService $productApplicationService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $output->writeln([
            '<info>Product setup</info>',
            '============',
            'This command will fetch the products from pretix for your configured event and will guide you through setting them up.',
            '<comment>This will delete all previous products config!</comment>',
            '<comment>Make sure you ran the \'setup:check-in-lists\' command first!</comment>',
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

        $this->deleteCurrentProducts();

        $items = $this->pretixItemRepository->getItems();

        $output->writeln([
            '',
            '<info>' . count($items) . ' products fetched from pretix</info>',
            ''
        ]);

        foreach ($items as $item) {
            $output->writeln([
                '<info>Product: ' . $item->getEnglishName() . '</info>',
                '============',
                ''
            ]);

            $question = new ConfirmationQuestion(
                '<question>Do you want to create a product for this item? (yes/no):</question> ',
                false
            );

            if (!$helper->ask($input, $output, $question)) {
                continue;
            }

            $productRequest = new ProductRequest(
                name: $item->getEnglishName(),
                pretixId: $item->id,
            );

            $product = $this->productApplicationService->createProduct($productRequest);

            $output->writeln([
                '',
                '<info>Product created with id ' . $product->getId() . '</info>',
                ''
            ]);
        }

        return Command::SUCCESS;
    }

    private function deleteCurrentProducts(): void
    {
        $products = $this->productRepository->findAll();

        foreach ($products as $product) {
            $this->entityManager->remove($product);
        }

        $this->entityManager->flush();
    }
}

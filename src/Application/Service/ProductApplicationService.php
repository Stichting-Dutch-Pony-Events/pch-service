<?php

namespace App\Application\Service;

use App\Application\Request\ProductRequest;
use App\DataAccessLayer\Repository\CheckInListRepository;
use App\Domain\Entity\Product;
use App\Domain\Service\ProductDomainService;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProductApplicationService
{
    public function __construct(
        private CheckInListRepository  $checkInListRepository,
        private EntityManagerInterface $entityManager,
        private ProductDomainService   $productDomainService
    ) {
    }

    public function createProduct(ProductRequest $productRequest): Product
    {
        $product = $this->productDomainService->createProduct($productRequest);

        $checkInLists = $this->checkInListRepository->findAll();
        foreach ($checkInLists as $checkInList) {
            if (in_array($product->getPretixId(), $checkInList->getPretixProductIds())) {
                $product->addCheckInList($checkInList);
            }
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }
}

<?php

namespace App\Domain\Service;

use App\Application\Request\ProductRequest;
use App\Domain\Entity\Product;

class ProductDomainService
{
    public function createProduct(ProductRequest $productRequest): Product
    {
        return new Product(
            name: $productRequest->name,
            pretixId: $productRequest->pretixId,
        );
    }
}

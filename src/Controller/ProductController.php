<?php

namespace App\Controller;

use App\Application\View\ProductView;
use App\DataAccessLayer\Repository\ProductRepository;
use App\Util\SymfonyUtils\Exception\WrongTypeException;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    /**
     * @throws ReflectionException
     * @throws WrongTypeException
     */
    #[OA\Response(
        response: 200,
        description: 'Products (all)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: ProductView::class
                )
            )
        )
    )]
    #[OA\Tag(name: 'Products')]
    public function all(): Response
    {
        return $this->json(
            Mapper::mapMany($this->productRepository->findAll(), ProductView::class),
            Response::HTTP_OK
        );
    }
}
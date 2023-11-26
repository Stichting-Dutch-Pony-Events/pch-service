<?php

namespace App\Controller;

use App\Application\View\UserView;
use App\Util\SymfonyUtils\Mapper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class UserController extends AbstractFOSRestController
{
    #[OA\Response(
        response: 200,
        description: 'User (self)',
        content: new OA\JsonContent(
            ref: new Model(
                type: UserView::class
            )
        )
    )]
    #[OA\Tag(name: 'User')]
    public function me(): Response {
        return $this->handleView($this->view(Mapper::mapOne($this->getUser(), UserView::class), 200));
    }
}
<?php

namespace App\Controller;

use App\Application\View\AttendeeView;
use App\Application\View\TeamView;
use App\DataAccessLayer\Repository\TeamRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\Team;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class TeamController extends AbstractController
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
    )
    {
    }

    #[OA\Response(
        response: 200,
        description: 'Teams (all)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: TeamView::class
                )
            )
        )
    )]
    #[OA\Tag(name: 'Team')]
    public function all(): Response
    {
        return $this->json(
            Mapper::mapMany($this->teamRepository->findAll(), TeamView::class),
            Response::HTTP_OK
        );
    }
}
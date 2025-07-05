<?php

namespace App\Controller;

use App\Application\Request\TeamRequest;
use App\Application\Service\TeamApplicationService;
use App\Application\View\TeamView;
use App\DataAccessLayer\Repository\TeamRepository;
use App\Security\Voter\TeamVoter;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TeamController extends AbstractController
{
    public function __construct(
        private readonly TeamApplicationService $teamApplicationService,
        private readonly TeamRepository         $teamRepository,
    ) {
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

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Create Team',
        content: new OA\JsonContent(
            ref: new Model(
                type: TeamView::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Bad Request',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Team Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: TeamRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Team')]
    #[IsGranted(TeamVoter::CREATE_TEAM)]
    public function createTeam(
        #[MapRequestPayload] TeamRequest $teamRequest
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->teamApplicationService->createTeam($teamRequest),
                TeamView::class
            ),
            Response::HTTP_CREATED
        );
    }

}
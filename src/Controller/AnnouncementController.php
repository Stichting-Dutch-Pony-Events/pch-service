<?php

namespace App\Controller;

use App\Application\Request\AnnouncementRequest;
use App\Application\Service\AnnouncementApplicationService;
use App\Application\View\AnnouncementView;
use App\DataAccessLayer\Repository\AnnouncementRepository;
use App\Security\Voter\AnnouncementVoter;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AnnouncementController extends AbstractController
{
    public function __construct(
        private AnnouncementRepository         $announcementRepository,
        private AnnouncementApplicationService $announcementApplicationService
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Announcements Last Ten',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: AnnouncementView::class
                )
            )
        )
    )]
    #[OA\Tag(name: 'Announcements')]
    public function lastTen(): Response
    {
        return $this->json(
            Mapper::mapMany($this->announcementRepository->lastTen(), AnnouncementView::class),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Create Announcement',
        content: new OA\JsonContent(
            ref: new Model(
                type: AnnouncementView::class
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
        description: 'Announcement Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: AnnouncementRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Announcements')]
    #[IsGranted(AnnouncementVoter::CREATE_ANNOUNCEMENT)]
    public function createAnnouncement(
        #[MapRequestPayload] AnnouncementRequest $announcementRequest
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->announcementApplicationService->createAnnouncement($announcementRequest),
                AnnouncementView::class
            ),
            Response::HTTP_CREATED
        );
    }
}
<?php

namespace App\Controller;

use App\Application\Request\ChangeOrderRequest;
use App\Application\Request\TimetableLocationRequest;
use App\Application\Service\TimetableLocationApplicationService;
use App\Application\View\TimetableLocationView;
use App\DataAccessLayer\Repository\TimetableLocationRepository;
use App\Domain\Entity\TimetableLocation;
use App\Domain\Enum\TimetableLocationType;
use App\Security\Voter\TimetableLocationVoter;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TimetableLocationController extends AbstractController
{
    public function __construct(
        private readonly TimetableLocationRepository         $timetableLocationRepository,
        private readonly TimetableLocationApplicationService $timetableLocationApplicationService,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Timetable Locations (by type)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: TimetableLocationView::class
                )
            )
        )
    )]
    #[IsGranted(TimetableLocationVoter::VIEW_LOCATION, subject: 'locationType')]
    #[OA\Tag(name: 'Timetable')]
    public function listTimetableLocations(TimetableLocationType $locationType): Response
    {
        return $this->json(
            Mapper::mapMany(
                $this->timetableLocationRepository->getByType($locationType),
                TimetableLocationView::class
            )
        );
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Create Timetable Location',
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableLocationView::class
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
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Timetable Day Not Found',
        content: new OA\JsonContent(
            ref: new Model(
                type: EntityNotFoundException::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Timetable Location Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableLocationRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    #[IsGranted(TimetableLocationVoter::CREATE_LOCATION)]
    public function createTimetableLocation(
        #[MapRequestPayload] TimetableLocationRequest $timetableLocationRequest
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->timetableLocationApplicationService->createTimetableLocation($timetableLocationRequest),
                TimetableLocationView::class
            ),
            Response::HTTP_CREATED
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Update Timetable Location',
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableLocationView::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Invalid Input',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Timetable Location Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableLocationRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    #[IsGranted(TimetableLocationVoter::EDIT_LOCATION, subject: 'timetableLocation')]
    public function updateTimetableLocation(
        #[MapEntity(id: 'timetableLocation')] TimetableLocation $timetableLocation,
        #[MapRequestPayload] TimetableLocationRequest           $timetableLocationRequest,
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->timetableLocationApplicationService->updateTimetableLocation(
                    $timetableLocation,
                    $timetableLocationRequest
                ),
                TimetableLocationView::class
            )
        );
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Change Timetable Location Order',
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
        description: 'Change Order Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: ChangeOrderRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    #[IsGranted(TimetableLocationVoter::CREATE_LOCATION)]
    public function changeOrder(
        #[MapRequestPayload] ChangeOrderRequest $changeOrderRequest
    ): Response {
        $this->timetableLocationApplicationService->changeOrder($changeOrderRequest);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Timetable Location Deleted',
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Invalid Input',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    #[IsGranted(TimetableLocationVoter::DELETE_LOCATION, subject: 'timetableLocation')]
    public function deleteTimetableLocation(
        #[MapEntity(id: 'timetableLocation')] TimetableLocation $timetableLocation,
    ): Response {
        $this->timetableLocationApplicationService->deleteTimetableLocation($timetableLocation);
        return new Response("", Response::HTTP_NO_CONTENT);
    }
}
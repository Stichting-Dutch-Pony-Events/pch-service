<?php

namespace App\Controller;

use App\Application\Request\TimetableItemRequest;
use App\Application\Service\TimetableItemApplicationService;
use App\Application\View\TimetableItemView;
use App\DataAccessLayer\Repository\TimetableDayRepository;
use App\DataAccessLayer\Repository\TimetableItemRepository;
use App\Domain\Entity\TimetableDay;
use App\Domain\Entity\TimetableItem;
use App\Domain\Enum\TimetableLocationType;
use App\Security\Voter\TimetableItemVoter;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TimetableItemController extends AbstractController
{
    public function __construct(
        private readonly TimetableItemApplicationService $timetableItemApplicationService,
        private readonly TimetableDayRepository          $timetableDayRepository,
        private readonly TimetableItemRepository         $timetableItemRepository,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Timetable Items',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: TimetableItemView::class
                )
            )
        )
    )]
    #[OA\QueryParameter(
        name: 'locationType',
        description: 'Order of the timetable days',
        required: true,
        schema: new OA\Schema(
            ref: new Model(type: TimetableLocationType::class),
            enum: ['ROOM', 'VOLUNTEER_POST']
        )
    )]
    #[OA\QueryParameter(
        name: 'timetableDay',
        description: 'The ID of the Timetable Day',
        required: true,
        schema: new OA\Schema(
            type: 'string',
            format: 'uuid',
            example: 'e7b8f1c2-3d4e-5f6a-7b8c-9d0e1f2a3b4c'
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    #[IsGranted(TimetableItemVoter::VIEW_ITEM, subject: 'locationType')]
    public function listTimetableItems(
        #[MapQueryParameter('locationType')] TimetableLocationType $locationType,
        #[MapQueryParameter('timetableDay')] string                $timetableDay
    ): Response {
        $timetableDayEntity = $this->timetableDayRepository->find($timetableDay);
        if (!$timetableDayEntity instanceof TimetableDay) {
            throw new EntityNotFoundException("Timetable Day not found.");
        }

        return $this->json(
            Mapper::mapMany(
                $this->timetableItemRepository->getByDayAndLocationType($timetableDayEntity, $locationType),
                TimetableItemView::class
            ),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Create Timetable Item',
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableItemView::class
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
        description: 'Relationship Not Found',
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
                type: TimetableItemRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    #[IsGranted(TimetableItemVoter::CREATE_ITEM)]
    public function createTimetableItem(
        #[MapRequestPayload] TimetableItemRequest $timetableItemRequest
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->timetableItemApplicationService->createTimetableItem($timetableItemRequest),
                TimetableItemView::class
            ),
            Response::HTTP_CREATED
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Update Timetable Item',
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableItem::class
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
        description: 'Timetable Item Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableItemRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    #[IsGranted(TimetableItemVoter::EDIT_ITEM, subject: 'timetableItem')]
    public function updateTimetableItem(
        #[MapEntity(id: 'timetableItem')] TimetableItem $timetableItem,
        #[MapRequestPayload] TimetableItemRequest       $timetableItemRequest,
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->timetableItemApplicationService->updateTimetableItem($timetableItem, $timetableItemRequest),
                TimetableItemView::class
            )
        );
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Timetable Item Deleted',
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
    #[IsGranted(TimetableItemVoter::DELETE_ITEM, subject: 'timetableItem')]
    public function deleteTimetableItem(
        #[MapEntity(id: 'timetableItem')] TimetableItem $timetableItem,
    ): Response {
        $this->timetableItemApplicationService->deleteTimetableItem($timetableItem);
        return new Response("", Response::HTTP_NO_CONTENT);
    }
}
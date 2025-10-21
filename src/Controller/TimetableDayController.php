<?php

namespace App\Controller;

use App\Application\Request\ChangeOrderRequest;
use App\Application\Request\TimetableDayRequest;
use App\Application\Service\TimetableDayApplicationService;
use App\Application\View\TimetableDayView;
use App\DataAccessLayer\Repository\TimetableDayRepository;
use App\Domain\Entity\TimetableDay;
use App\Security\Voter\TimetableDayVoter;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TimetableDayController extends AbstractController
{
    public function __construct(
        private readonly TimetableDayRepository         $timetableDayRepository,
        private readonly TimetableDayApplicationService $timetableDayApplicationService,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Timetable Days (all)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: TimetableDayView::class
                )
            )
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    public function all(): Response
    {
        return $this->json(
            Mapper::mapMany($this->timetableDayRepository->getOrdered(), TimetableDayView::class),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Create Timetable Day',
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableDayView::class
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
        description: 'Timetable Day Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableDayRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    #[IsGranted(TimetableDayVoter::CREATE_DAY)]
    public function createTimetableDay(
        #[MapRequestPayload] TimetableDayRequest $timetableDayRequest
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->timetableDayApplicationService->createTimetableDay($timetableDayRequest),
                TimetableDayView::class
            ),
            Response::HTTP_CREATED
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Update Timetable Day',
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableDayView::class
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
        description: 'Timetable Day Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: TimetableDayRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    #[IsGranted(TimetableDayVoter::EDIT_DAY, subject: 'timetableDay')]
    public function updateTimetableDay(
        #[MapEntity(id: 'timetableDay')] TimetableDay $timetableDay,
        #[MapRequestPayload] TimetableDayRequest      $timetableDayRequest,
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->timetableDayApplicationService->updateTimetableDay($timetableDay, $timetableDayRequest),
                TimetableDayView::class
            )
        );
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Change Timetable Day Order',
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
    #[IsGranted(TimetableDayVoter::EDIT_DAY)]
    public function changeOrder(
        #[MapRequestPayload] ChangeOrderRequest $changeOrderRequest
    ): Response {
        $this->timetableDayApplicationService->changeOrder($changeOrderRequest);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Timetable Day Deleted',
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
    #[IsGranted(TimetableDayVoter::DELETE_DAY, subject: 'timetableDay')]
    public function deleteTimetableDay(
        #[MapEntity(id: 'timetableDay')] TimetableDay $timetableDay,
    ): Response {
        $this->timetableDayApplicationService->deleteTimetableDay($timetableDay);
        return new Response("", Response::HTTP_NO_CONTENT);
    }
}
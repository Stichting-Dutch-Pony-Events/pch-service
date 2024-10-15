<?php

namespace App\Controller;

use App\Application\Request\AwardAchievementRequest;
use App\Application\Request\DispatchPrintJobRequest;
use App\Application\Request\SetPasswordRequest;
use App\Application\Request\SetPrintJobStatusRequest;
use App\Application\Service\AttendeeApplicationService;
use App\Application\Service\PrintJobApplicationService;
use App\Application\View\AttendeeAchievementView;
use App\Application\View\PrintJobView;
use App\Application\View\TeamView;
use App\DataAccessLayer\Repository\PrintJobRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\PrintJob;
use App\Security\Voter\AchievementVoter;
use App\Security\Voter\AttendeeVoter;
use App\Security\Voter\PrintJobVoter;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PrintJobController extends AbstractController
{
    public function __construct(
        private PrintJobRepository         $printJobRepository,
        private PrintJobApplicationService $printJobApplicationService,
        private AttendeeApplicationService $attendeeApplicationService,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'PrintJobs (pending)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: PrintJobView::class
                )
            )
        )
    )]
    #[OA\Tag(name: 'PrintJobs')]
    #[IsGranted(PrintJobVoter::VIEW)]
    public function getPendingJobs(): Response
    {
        return $this->json(
            Mapper::mapMany($this->printJobRepository->getPrintablePrintJobs(), PrintJobView::class),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Print Job Created for Attendee',
        content: new OA\JsonContent(
            ref: new Model(
                type: PrintJobView::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Dispatch Print Job Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: DispatchPrintJobRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'PrintJobs')]
    #[IsGranted(PrintJobVoter::CREATE)]
    public function createPrintJob(
        #[MapRequestPayload] DispatchPrintJobRequest $dispatchPrintJobRequest
    ): Response {
        return $this->json(
            Mapper::mapOne($this->printJobApplicationService->createPrintJob($dispatchPrintJobRequest), PrintJobView::class),
            Response::HTTP_CREATED
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Print Job Status Updated',
        content: new OA\JsonContent(
            ref: new Model(
                type: PrintJobView::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'PrintJob Already has this status',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'SetPrintJobStatusRequest',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: SetPrintJobStatusRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'PrintJobs')]
    #[IsGranted(PrintJobVoter::EDIT, 'printJob')]
    public function setPrintJobStatus(
        #[MapEntity(id: 'id')] PrintJob               $printJob,
        #[MapRequestPayload] SetPrintJobStatusRequest $setPrintJobStatusRequest
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->printJobApplicationService->setPrintJobStatus($printJob, $setPrintJobStatusRequest),
                PrintJobView::class
            ),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: 200,
        description: 'PrintJob Graphic',
    )]
    #[OA\Tag(name: 'PrintJobs')]
    #[IsGranted(PrintJobVoter::EDIT, subject: 'printJob')]
    public function getPrintJobGraphic(
        #[MapEntity(id: 'id')] PrintJob $printJob,
    ) {
        return new Response(
            $this->attendeeApplicationService->getAttendeeBadge($printJob->getAttendee()),
            Response::HTTP_OK,
            [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'inline; filename="' . $printJob->getId() . '.png"'
            ]
        );
    }
}
<?php

namespace App\Controller\Public;

use App\Application\Response\PublicTimetableResponse;
use App\Application\Service\Public\PublicTimetableApplicationService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TimetableController extends AbstractController
{
    public function __construct(
        private readonly PublicTimetableApplicationService $publicTimetableApplicationService
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Public Timetable',
        content: new OA\JsonContent(
            ref: new Model(
                     type: PublicTimetableResponse::class
                 )
        )
    )]
    #[OA\Tag(name: 'Timetable')]
    public function getPublicTimetable(): Response
    {
        $timetable = $this->publicTimetableApplicationService->getPublicTimeTable();

        return $this->json($timetable);
    }
}
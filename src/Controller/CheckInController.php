<?php

namespace App\Controller;

use App\Application\Request\CheckInRequest;
use App\Application\Response\CheckInResponse;
use App\Application\Service\CheckInApplicationService;
use App\Security\Voter\CheckInVoter;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use Illuminate\Validation\ValidationException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CheckInController extends AbstractController
{
    public function __construct(
        private readonly CheckInApplicationService $checkInApplicationService
    ) {
    }

    /**
     * @throws ValidationException
     */
    #[OA\Response(
        response: 200,
        description: 'Perform Check-In',
        content: new OA\JsonContent(
            ref: new Model(
                type: CheckInResponse::class
            )
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Unprocessable Entity',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Check-In request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: CheckInRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Check-In')]
    #[IsGranted(CheckInVoter::CHECK_IN)]
    public function checkIn(
        #[MapRequestPayload] CheckInRequest $checkInRequest,
    ): Response {
        return $this->json($this->checkInApplicationService->performCheckIn($checkInRequest));
    }
}

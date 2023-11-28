<?php

namespace App\Controller;

use App\Application\Request\CheckInRequest;
use App\Application\Response\CheckInResponse;
use App\Application\Service\CheckInApplicationService;
use App\Domain\Enum\CheckInListType;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\Validator\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

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
    public function checkIn(
        #[MapRequestPayload] CheckInRequest $checkInRequest,
        Request                             $request
    ): Response {
        Validator::validate($request, [
            'secret'   => 'required|string',
            'listType' => [
                'required',
                'string',
                Rule::enum(CheckInListType::class)
            ]
        ]);

        return $this->json($this->checkInApplicationService->performCheckIn($checkInRequest));
    }
}

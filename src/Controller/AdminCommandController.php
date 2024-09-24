<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Request\AdminCommandRequest;
use App\Application\Request\SetPasswordRequest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class AdminCommandController extends AbstractController
{
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Command Executed'
    )]
    #[OA\RequestBody(
        description: 'Set Password Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: SetPasswordRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Attendee')]
    public function runAdminCommand(
        #[MapRequestPayload] AdminCommandRequest $request
    ): Response
    {
        $this->denyAccessUnlessGranted($request->commandType->value);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

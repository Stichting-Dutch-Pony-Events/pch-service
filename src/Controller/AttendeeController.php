<?php

namespace App\Controller;

use App\Application\Request\AttendeeRequest;
use App\Application\Request\SetPasswordRequest;
use App\Application\Service\AttendeeApplicationService;
use App\Application\View\AttendeeView;
use App\Domain\Entity\Attendee;
use App\Security\Voter\AttendeeVoter;
use App\Util\BadgeGenerator;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Mapper;
use App\Util\Validator\Validator;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AttendeeController extends AbstractController
{
    public function __construct(
        private readonly AttendeeApplicationService $attendeeApplicationService
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Attendee (self)',
        content: new OA\JsonContent(
            ref: new Model(
                type: AttendeeView::class
            )
        )
    )]
    #[OA\Tag(name: 'Attendee')]
    public function me(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Attendee) {
            throw new EntityNotFoundException('Attendee not found');
        }

        return $this->json(Mapper::mapOne($user, AttendeeView::class));
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Attendee',
        content: new OA\JsonContent(
            ref: new Model(
                type: AttendeeView::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Attendee Not Found',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\Tag(name: 'Attendee')]
    #[IsGranted(AttendeeVoter::VIEW, subject: 'attendee')]
    public function find(string $identifier): Response
    {
        return $this->json(
            Mapper::mapOne($this->attendeeApplicationService->find($identifier), AttendeeView::class),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: 200,
        description: 'Attendee Image',
    )]
    #[OA\Tag(name: 'Attendee')]
    #[IsGranted(AttendeeVoter::VIEW, subject: 'attendee')]
    public function getAttendeeImage(
        #[MapEntity(id: 'attendee')] Attendee $attendee
    ): Response {
        return new Response($this->attendeeApplicationService->getAttendeeBadge($attendee), Response::HTTP_OK, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'inline; filename="' . $attendee->getId() . '.png"'
        ]);
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Password updated'
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
        description: 'Set Password Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: SetPasswordRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Attendee')]
    public function updatePassword(
        #[MapRequestPayload] SetPasswordRequest $setPasswordRequest,
        Request                                 $request
    ): Response {
        Validator::validate($request, [
            'password'             => 'required|string|numeric|min:4',
            'passwordConfirmation' => 'required|string|numeric|min:4',
            'currentPassword'      => 'required|string',
        ]);

        $user = $this->getUser();

        if (!$user instanceof Attendee) {
            throw new EntityNotFoundException('Attendee not found');
        }

        $this->attendeeApplicationService->updatePassword($user, $setPasswordRequest);

        return new Response("", Response::HTTP_NO_CONTENT);
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Attendee',
        content: new OA\JsonContent(
            ref: new Model(
                type: AttendeeView::class
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
        description: 'Attendee Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: AttendeeRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Attendee')]
    #[IsGranted(AttendeeVoter::EDIT, subject: 'attendee')]
    public function updateAttendee(
        #[MapEntity(id: 'attendee')] Attendee $attendee,
        #[MapRequestPayload] AttendeeRequest  $attendeeRequest,
        Request                               $request
    ): Response {
        $this->denyAccessUnlessGranted(AttendeeVoter::EDIT, $attendee);

        $user = $this->getUser();

        if (!$user instanceof Attendee) {
            throw new EntityNotFoundException('Attendee not found');
        }

        return $this->json(
            Mapper::mapOne(
                $this->attendeeApplicationService->updateAttendee($attendee, $attendeeRequest),
                AttendeeView::class
            )
        );
    }
}

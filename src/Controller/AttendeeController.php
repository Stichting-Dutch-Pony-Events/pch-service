<?php

namespace App\Controller;

use App\Application\Request\AttendeeRequest;
use App\Application\Request\AttendeeSearchRequest;
use App\Application\Request\SetAttendeeRolesRequest;
use App\Application\Request\SetPasswordRequest;
use App\Application\Response\AttendeeSearchResponse;
use App\Application\Service\AttendeeApplicationService;
use App\Application\View\AttendeeView;
use App\DataAccessLayer\Repository\AttendeeRepository;
use App\Domain\Entity\Attendee;
use App\Security\Voter\AttendeeVoter;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Exception\WrongTypeException;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use ReflectionException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AttendeeController extends AbstractController
{
    public function __construct(
        private readonly AttendeeApplicationService $attendeeApplicationService,
        private readonly AttendeeRepository         $attendeeRepository
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
    public function find(string $identifier): Response
    {
        $attendee = $this->attendeeApplicationService->find($identifier);
        $this->denyAccessUnlessGranted(AttendeeVoter::VIEW, $attendee);

        return $this->json(
            Mapper::mapOne($attendee, AttendeeView::class),
            Response::HTTP_OK
        );
    }

    /**
     * @throws ReflectionException
     * @throws WrongTypeException
     */
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Attendees',
        content: new OA\JsonContent(
            ref: new Model(
                     type: AttendeeSearchResponse::class
                 )
        )
    )]
    #[OA\QueryParameter(
        name: 'query',
        description: 'Search query, filter by name, email or nickname',
        required: true,
        schema: new OA\Schema(
            type: 'string',
        )
    )]
    #[OA\QueryParameter(
        name: 'productId',
        description: 'Filter by product ID',
        required: true,
        schema: new OA\Schema(
            type:    'string',
            format:  'uuid',
            example: '01987001-4494-743e-8497-95f3c416cc53'
        )
    )]
    #[OA\QueryParameter(
        name: 'role',
        description: 'Filter by Role',
        required: true,
        schema: new OA\Schema(
            type:    'string',
            enum:    ['ROLE_USER', 'ROLE_VOLUNTEER', 'ROLE_INFOBOOTH', 'ROLE_STAFF', 'ROLE_SUPER_ADMIN'],
            example: 'ROLE_VOLUNTEER'
        )
    )]
    #[OA\QueryParameter(
        name: 'page',
        description: 'Page number for pagination',
        required: true,
        schema: new OA\Schema(
            type:    'integer',
            default: 1
        )
    )]
    #[OA\QueryParameter(
        name: 'itemsPerPage',
        description: 'Number of results per page',
        required: true,
        schema: new OA\Schema(
            type:    'integer',
            default: 10
        )
    )]
    #[OA\QueryParameter(
        name: 'sortBy',
        description: 'Field to sort by',
        required: false,
        schema: new OA\Schema(
            type:    'string',
            default: 'name:asc',
            example: 'name:asc,email:desc'
        )
    )]
    #[OA\Tag(name: 'Attendee')]
    #[IsGranted(AttendeeVoter::VIEW)]
    public function listAttendees(
        #[MapQueryString] AttendeeSearchRequest $attendeeSearchRequest,
    ): Response {
        return $this->json(
            $this->attendeeRepository->searchAttendees($attendeeSearchRequest),
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
        #[MapEntity(id: 'attendee')] Attendee $attendee,
        #[MapQueryParameter('cache')] bool    $cache = true
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
    ): Response {
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

        return $this->json(
            Mapper::mapOne(
                $this->attendeeApplicationService->updateAttendee($attendee, $attendeeRequest),
                AttendeeView::class
            )
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Attendee Roles',
        content: new OA\JsonContent(
            ref: new Model(
                     type: AttendeeView::class
                 )
        )
    )]
    #[OA\RequestBody(
        description: 'Set Attendee Roles Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                     type: SetAttendeeRolesRequest::class
                 )
        )
    )]
    #[OA\Tag(name: 'Attendee')]
    #[IsGranted(AttendeeVoter::EDIT_ROLES, subject: 'attendee')]
    public function setAttendeeRoles(
        #[MapEntity(id: 'attendee')] Attendee        $attendee,
        #[MapRequestPayload] SetAttendeeRolesRequest $setAttendeeRolesRequest,
        Request                                      $request
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->attendeeApplicationService->setAttendeeRoles($attendee, $setAttendeeRolesRequest),
                AttendeeView::class
            )
        );
    }
}

<?php

namespace App\Controller;

use App\Application\Service\AttendeeApplicationService;
use App\Application\View\AttendeeView;
use App\Domain\Entity\Attendee;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

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

        if(!$user instanceof Attendee) {
            throw new EntityNotFoundException('Attendee not found');
        }

        return new JsonResponse(Mapper::mapOne($user, AttendeeView::class));
    }
}

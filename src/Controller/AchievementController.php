<?php

namespace App\Controller;

use App\Application\Request\AwardAchievementRequest;
use App\Application\Request\UnlockAchievementRequest;
use App\Application\Service\AchievementApplicationService;
use App\Application\View\AchievementView;
use App\Application\View\AttendeeAchievementView;
use App\DataAccessLayer\Repository\AchievementRepository;
use App\Domain\Entity\Achievement;
use App\Domain\Entity\Attendee;
use App\Security\AchievementVoter;
use App\Util\Exceptions\Response\PublicExceptionResponse;
use App\Util\SymfonyUtils\Mapper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AchievementController extends AbstractController
{
    public function __construct(
        private readonly AchievementRepository         $achievementRepository,
        private readonly AchievementApplicationService $achievementApplicationService,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Achievements (all)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: AchievementView::class
                )
            )
        )
    )]
    #[OA\Tag(name: 'Achievements')]
    public function all(): Response
    {
        return $this->json(
            Mapper::mapMany($this->achievementRepository->findAll(), AchievementView::class),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Award Achievement to Attendee',
        content: new OA\JsonContent(
            ref: new Model(
                type: AttendeeAchievementView::class
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
    #[OA\Response(
        response: Response::HTTP_EXPECTATION_FAILED,
        description: 'Attendee already has achievement',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Award Achievement Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: AwardAchievementRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Achievements')]
    #[IsGranted(AchievementVoter::AWARD_ACHIEVEMENT)]
    public function awardAchievement(
        #[MapEntity(id: 'achievement')] Achievement  $achievement,
        #[MapRequestPayload] AwardAchievementRequest $awardAchievementRequest
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->achievementApplicationService->awardAchievement($achievement, $awardAchievementRequest),
                AttendeeAchievementView::class
            ),
            Response::HTTP_CREATED
        );
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Award Achievement through unlock code',
        content: new OA\JsonContent(
            ref: new Model(
                type: AttendeeAchievementView::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'No Achievement with submitted code found',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\Response(
        response: Response::HTTP_EXPECTATION_FAILED,
        description: 'Attendee already has achievement',
        content: new OA\JsonContent(
            ref: new Model(
                type: PublicExceptionResponse::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Unlock Achievement Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: UnlockAchievementRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Achievements')]
    public function unlockAchievement(
        #[MapRequestPayload] UnlockAchievementRequest $unlockAchievementRequest,
    ): Response {
        /** @var Attendee $user */
        $user = $this->getUser();

        return $this->json(
            Mapper::mapOne(
                $this->achievementApplicationService->unlockAchievement($user, $unlockAchievementRequest),
                AttendeeAchievementView::class
            ),
            Response::HTTP_CREATED
        );
    }
}
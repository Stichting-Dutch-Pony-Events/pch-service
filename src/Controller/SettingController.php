<?php

namespace App\Controller;

use App\Application\Request\SettingRequest;
use App\Application\Service\SettingApplicationService;
use App\Application\View\SettingView;
use App\DataAccessLayer\Repository\SettingRepository;
use App\Security\Voter\SettingVoter;
use App\Util\SymfonyUtils\Mapper;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SettingController extends AbstractController
{
    public function __construct(
        private SettingRepository         $settingRepository,
        private SettingApplicationService $settingApplicationService,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Settings (all)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: SettingView::class
                )
            )
        )
    )]
    #[OA\Tag(name: 'Setting')]
    public function all(): Response
    {
        return $this->json(Mapper::mapMany($this->settingRepository->findAll(), SettingView::class), Response::HTTP_OK);
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Setting',
        content: new OA\JsonContent(
            ref: new Model(
                type: SettingView::class
            )
        )
    )]
    #[OA\RequestBody(
        description: 'Setting Request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: SettingRequest::class
            )
        )
    )]
    #[OA\Tag(name: 'Setting')]
    #[IsGranted(SettingVoter::EDIT_SETTINGS)]
    public function updateSetting(
        #[MapRequestPayload] SettingRequest $settingRequest,
    ): Response {
        return $this->json(
            Mapper::mapOne(
                $this->settingApplicationService->setSetting($settingRequest),
                SettingView::class
            )
        );
    }
}
<?php

namespace App\Util;

use App\DataAccessLayer\Pretix\Repositories\OrderRepository;
use App\Domain\Entity\Attendee;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use Imagick;
use ImagickDraw;
use ImagickPixel;

class BadgeGenerator
{
    private Imagick $image;

    public function __construct(
        private OrderRepository $orderRepository
    ) {
    }

    public function generate(Attendee $attendee): string
    {
        $imagePath = __DIR__ . '/../../assets/badges/' . $attendee->getProduct()->getPretixId() . '.png';
        $dataPath  = __DIR__ . '/../../assets/badges/' . $attendee->getProduct()->getPretixId() . '.json';

        if (!file_exists($imagePath) || !file_exists($dataPath)) {
            throw new EntityNotFoundException('Badges not found');
        }

        $data = json_decode(file_get_contents($dataPath));

        $image = new Imagick($imagePath);

        $orderPosition = $this->orderRepository->getOrderPosition($attendee->getTicketId());
        $ocImageUrl    = $orderPosition->getAnswer('OC-IMAGE');

        if ($ocImageUrl) {
            $tmpPath = __DIR__ . '/../../var/tmp/' . $attendee->getProduct()->getPretixId() . '.png';

            $this->orderRepository->downloadUrl($ocImageUrl, $tmpPath);

            $ocImage = new Imagick($tmpPath);
            $ocImage->resizeImage($data->profileWidth, $data->profileHeight, Imagick::FILTER_LANCZOS, 1, true);
            $image->compositeImage($ocImage, Imagick::COMPOSITE_OVER, $data->profileX, $data->profileY);
        }

        $fontFile = __DIR__ . '/../../assets/badges/' . $data->font;

        $image->setImageFormat('png');
        return $image->getImageBlob();
    }
}

<?php

namespace App\Util;

use App\DataAccessLayer\Pretix\Repositories\OrderRepository;
use App\Domain\Entity\Attendee;
use App\Util\Exceptions\Exception\Common\InvalidInputException;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use Exception;
use GdImage;
use GuzzleHttp\Exception\ClientException;
use JsonException;
use jucksearm\barcode\QRcode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class BadgeGenerator
{
    public function __construct(
        private OrderRepository $orderRepository,
        private string          $projectDir,
        private Filesystem      $filesystem,
    ) {
    }

    /**
     * @throws EntityNotFoundException
     * @throws InvalidInputException
     * @throws JsonException
     */
    public function generate(Attendee $attendee): string
    {
        $product = $attendee->getOverrideBadgeProduct() ?? $attendee->getProduct();

        $imagePath = __DIR__.'/../../assets/badges/'.$product->getPretixId().'.png';
        $dataPath  = __DIR__.'/../../assets/badges/'.$product->getPretixId().'.json';

        if (!file_exists($imagePath) || !file_exists($dataPath)) {
            throw new EntityNotFoundException('Badges not found for: '.$product->getName());
        }

        $data = json_decode($this->filesystem->readFile($dataPath), false, 512, JSON_THROW_ON_ERROR);

        try {
            $orderPosition = $this->orderRepository->getOrderPosition($attendee->getTicketId());
        } catch (ClientException $e) {
            throw new EntityNotFoundException('Badges not found for: '.$attendee->getTicketId());
        }

        $ocImageUrl = $orderPosition->getAnswer('OC-IMAGE');

        $image = imagecreatefrompng($imagePath);
        if ($image === false) {
            throw new InvalidInputException('Could not create image from badge template: '.$imagePath);
        }

        imagesavealpha($image, true);

        $useDefault = true;

        if ($ocImageUrl) {
            try {
                $tmpPath = __DIR__.'/../../var/tmp/'.$product->getPretixId();

                $tmpPath = $this->orderRepository->downloadImage($ocImageUrl, $tmpPath);

                $ocPath = $tmpPath;

                if (str_ends_with($ocPath, '.png')) {
                    $ocImage = @imagecreatefrompng($ocPath);
                } elseif (str_ends_with($ocPath, '.jpg') || str_ends_with($ocPath, '.jpeg')) {
                    $ocImage = imagecreatefromjpeg($ocPath);
                } else {
                    throw new InvalidInputException("Image should be jpeg or png, this fucker aint getting a badge");
                }

                if ($ocImage) {
                    imagesavealpha($ocImage, true);
                    $ocImage = $this->resizeImage($ocImage, $data->profileWidth, $data->profileHeight);
                    $posX    = (int)round(($data->profileWidth - imagesx($ocImage)) / 2) + $data->profileX;
                    $posY    = (int)round(($data->profileHeight - imagesy($ocImage)) / 2) + $data->profileY;

                    imagecopy($image, $ocImage, $posX, $posY, 0, 0, imagesx($ocImage), imagesy($ocImage));

                    $useDefault = false;
                }
            } catch (Exception $e) {
                // If the image could not be created, we will use the default image
                error_log('Error creating OC image: '.$e->getMessage());
            }
        }

        if ($useDefault) {
            $ocPath  = __DIR__.'/../../assets/badges/unknown.png';
            $ocImage = imagecreatefrompng($ocPath);
            if ($ocImage === false) {
                throw new InvalidInputException('Could not create image from default badge template: '.$ocPath);
            }

            imagesavealpha($ocImage, true);
            $ocImage = $this->resizeImage($ocImage, $data->profileWidth, $data->profileHeight);
            $posX    = ($data->profileWidth - imagesx($ocImage)) / 2 + $data->profileX;
            $posY    = ($data->profileHeight - imagesy($ocImage)) / 2 + $data->profileY;

            imagecopy($image, $ocImage, $posX, $posY, 0, 0, imagesx($ocImage), imagesy($ocImage));
        }

        $fontFile = __DIR__.'/../../assets/badges/'.$data->font;
        if (file_exists($fontFile) && $attendee->getNickName()) {
            $fontSize = $this->getMaxFontSize($fontFile, $attendee->getNickName(), $data->nameWidth, $data->nameHeight);
            $color    = imagecolorallocate($image, 50, 50, 50);
            if ($color === false) {
                throw new InvalidInputException('Could not allocate color for nickname text');
            }

            $boxSize = imagettfbbox($fontSize, 0, $fontFile, $attendee->getNickName());
            if ($boxSize === false) {
                throw new InvalidInputException(
                    'Could not calculate text box size for nickname: '.$attendee->getNickName()
                );
            }
            $width  = $boxSize[2] - $boxSize[0];
            $height = $boxSize[1] - $boxSize[7];
            $posX   = (int)round(($data->nameWidth - $width) / 2) + $data->nameX;
            $posY   = (int)round(($data->nameHeight - $height) / 2) + $data->nameY + $fontSize;

            imagettftext($image, $fontSize, 0, $posX, $posY, $color, $fontFile, $attendee->getNickName());
        }

        $qrCode = imagecreatefromstring($this->getDataMatrix($attendee->getMiniIdentifier() ?? '', $data->qrSize));
        if ($qrCode === false) {
            throw new InvalidInputException('Could not create QR code image from data matrix');
        }

        $qrWidth  = imagesx($qrCode);
        $qrHeight = imagesy($qrCode);
        $posX     = $data->qrX - $qrWidth;
        $posY     = $data->qrY - $qrHeight;
        imagecopy($image, $qrCode, $posX, $posY, 0, 0, $qrWidth, $qrHeight);

        $filename = $this->getFileName($attendee);

        imagepng($image, $filename);

        imagedestroy($image);
        if (isset($ocImage)) {
            imagedestroy($ocImage);
        }
        imagedestroy($qrCode);

        $attendee->setBadgeFile($filename);

        return $filename;
        /*
        $fontFile = __DIR__ . '/../../assets/badges/' . $data->font;

        $image->setImageFormat('png');
        return $image->getImageBlob();
        */
    }

    private function resizeImage(GdImage $image, int $max_width, int $max_height): GdImage
    {
        $orig_width  = imagesx($image);
        $orig_height = imagesy($image);

        $width  = $orig_width;
        $height = $orig_height;

        # taller
        if ($height > $max_height) {
            $width  = (int)round(($max_height / $height) * $width);
            $height = $max_height;
        }

        # wider
        if ($width > $max_width) {
            $height = (int)round(($max_width / $width) * $height);
            $width  = $max_width;
        }

        $image_p = imagecreatetruecolor($width, $height);
        imagealphablending($image_p, false);
        $transparency = imagecolorallocatealpha($image_p, 0, 0, 0, 127);
        if ($transparency === false) {
            throw new InvalidInputException('Could not allocate transparency color for resized image');
        }
        imagefill($image_p, 0, 0, $transparency);
        imagesavealpha($image_p, true);

        imagecopyresampled(
            $image_p,
            $image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $orig_width,
            $orig_height
        );

        return $image_p;
    }

    public function getMaxFontSize(
        string $fontFile,
        string $text,
        int    $maxWidth,
        int    $maxHeight,
        int    $maxSize = 100
    ): int {
        $size = $maxSize;

        do {
            $size--;
            $box = imagettfbbox($size, 0, $fontFile, $text);
            if ($box === false) {
                throw new InvalidInputException('Invalid font size provided');
            }
        } while ($size > 0 && ($box[2] - $box[0] > $maxWidth || $box[1] - $box[7] > $maxHeight));

        return $size;
    }

    public function getDataMatrix(string $miniIdentifier, int $size): string
    {
        return QRcode::factory()
            ->setCode($miniIdentifier)
            ->setMargin(5)
            ->setSize($size)
            ->setLevel('H')
            ->getQRcodePngData();
    }

    public function getFileName(Attendee $attendee): string
    {
        $file = Path::join($this->projectDir, '/var/badges/', $attendee->getId().'.png');
        $this->filesystem->mkdir(Path::getDirectory($file));
        return $file;
    }
}

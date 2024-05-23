<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Clipping;
use Imagick;
use ImagickPixel;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader {
    public function __construct(
        private string $imageDir,
        private int $thumbWidth,
        private int $thumbHeight
    ) {}

    public function upload(UploadedFile $file) : string {
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safe = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $original);
        $filename = $safe . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($this->imageDir, $filename);

        return $filename;
    }

    public function getImageDir() : string {
        return $this->imageDir;
    }

    public function thumbnail(Clipping $clipping) : string {
        $file = $clipping->getImageFile();
        $thumbName = $file->getBasename('.' . $file->getExtension()) . '_tn.png';
        $magick = new Imagick($file->getPathname());

        $magick->setBackgroundColor(new ImagickPixel('white'));
        $magick->thumbnailimage($this->thumbWidth, $this->thumbHeight, true, true);
        $magick->setImageFormat('png32');

        $handle = fopen($file->getPath() . '/' . $thumbName, 'wb');
        fwrite($handle, $magick->getimageblob());

        return $thumbName;
    }

    public function processClipping(Clipping $clipping) : void {
        $uploadedFile = $clipping->getImageFile();
        if ( ! $uploadedFile instanceof UploadedFile) {
            return;
        }
        $clipping->setOriginalName($uploadedFile->getClientOriginalName());

        $filename = $this->upload($uploadedFile);
        $clippingFile = new File($this->imageDir . '/' . $filename);
        $clipping->setImageFile($clippingFile);
        $clipping->setImageSize($clippingFile->getSize());
        $clipping->setImageFilePath($this->imageDir . '/' . $filename);
        $clipping->setThumbnailPath($this->thumbnail($clipping));
    }

    public function getMaxUploadSize(bool $asBytes = true) : float|int|string {
        static $maxBytes = -1;

        if ($maxBytes < 0) {
            $postMax = $this->parseSize(ini_get('post_max_size'));
            if ($postMax > 0) {
                $maxBytes = $postMax;
            }

            $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
            if ($uploadMax > 0 && $uploadMax < $maxBytes) {
                $maxBytes = $uploadMax;
            }
        }
        if ($asBytes) {
            return $maxBytes;
        }
        $units = ['b', 'Kb', 'Mb', 'Gb', 'Tb'];
        $exp = floor(log($maxBytes, 1024));
        $est = round($maxBytes / 1024 ** $exp, 1);

        return $est . $units[$exp];
    }

    public function parseSize(string $size) : float {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $bytes = (float) preg_replace('/[^\d\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($bytes * 1024 ** mb_stripos('bkmgtpezy', $unit[0]));
        }

        return round($bytes);
    }
}

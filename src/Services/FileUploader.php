<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Services;

use App\Entity\Clipping;
use Imagick;
use ImagickPixel;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of FileUploader.
 *
 * @author Michael Joyce <ubermichael@gmail.com>
 */
class FileUploader
{
    /**
     * @var string
     */
    private $imageDir;

    private $thumbWidth;

    private $thumbHeight;

    public function __construct($imageDir, $thumbWidth, $thumbHeight) {
        $this->imageDir = $imageDir;
        $this->thumbHeight = $thumbHeight;
        $this->thumbWidth = $thumbWidth;
    }

    public function upload(UploadedFile $file) {
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safe = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $original);
        $filename = $safe . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($this->imageDir, $filename);

        return $filename;
    }

    /**
     * @return string
     */
    public function getImageDir() {
        return $this->imageDir;
    }

    public function thumbnail(Clipping $clipping) {
        $file = $clipping->getImageFile();
        $thumbname = $file->getBasename('.' . $file->getExtension()) . '_tn.png';
        $magick = new Imagick($file->getPathname());

        $magick->setBackgroundColor(new ImagickPixel('white'));
        $magick->thumbnailimage($this->thumbWidth, $this->thumbHeight, true, true);
        $magick->setImageFormat('png32');

        $handle = fopen($file->getPath() . '/' . $thumbname, 'wb');
        fwrite($handle, $magick->getimageblob());

        return $thumbname;
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

    public function getMaxUploadSize($asBytes = true) {
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

    public function parseSize($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $bytes = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($bytes * 1024 ** mb_stripos('bkmgtpezy', $unit[0]));
        }

        return round($bytes);
    }
}

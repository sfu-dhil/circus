<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Clipping.
 *
 * @ORM\Table(name="clipping", indexes={
 *     @ORM\Index(columns="transcription", flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\ClippingRepository")
 */
class Clipping extends AbstractEntity {
    /**
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private string $originalName;

    private File $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $imageFilePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $thumbnailPath;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $imageSize;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $imageWidth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $imageHeight;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private string $number;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private string $writtenDate;

    /**
     * YYYY-MM-DD.
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Regex(pattern="/\d{4}-\d{2}-\d{2}/", message="The format must be YYYY-MM-DD. The year must be four digits. Month and day must be two digits.")
     */
    private string $date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string $transcription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string $annotations;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="clippings")
     * @ORM\JoinColumn(nullable=false)
     */
    private Category $category;

    /**
     * @ORM\ManyToOne(targetEntity="Source", inversedBy="clippings")
     * @ORM\JoinColumn(nullable=false)
     */
    private Source $source;

    public function __toString() : string {
        return $this->originalName;
    }

    /**
     * Set originalName.
     */
    public function setOriginalName(string $originalName) : self {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get originalName.
     */
    public function getOriginalName() : string {
        return $this->originalName;
    }

    /**
     * Get the image file.
     */
    public function getImageFile() : File {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile) {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * Set imageFilePath.
     */
    public function setImageFilePath(string $imageFilePath) : self {
        $this->imageFilePath = $imageFilePath;

        return $this;
    }

    /**
     * Get imageFilePath.
     */
    public function getImageFilePath() : string {
        return $this->imageFilePath;
    }

    /**
     * Set imageSize.
     */
    public function setImageSize(int $imageSize) : self {
        $this->imageSize = $imageSize;

        return $this;
    }

    /**
     * Get imageSize.
     */
    public function getImageSize() : int {
        return $this->imageSize;
    }

    /**
     * Set imageWidth.
     */
    public function setImageWidth(int $imageWidth) : self {
        $this->imageWidth = $imageWidth;

        return $this;
    }

    /**
     * Get imageWidth.
     */
    public function getImageWidth() : int {
        return $this->imageWidth;
    }

    /**
     * Set imageHeight.
     */
    public function setImageHeight(int $imageHeight) : self {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    /**
     * Get imageHeight.
     */
    public function getImageHeight() : int {
        return $this->imageHeight;
    }

    /**
     * Set number.
     */
    public function setNumber(string $number) : self {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     */
    public function getNumber() : string {
        return $this->number;
    }

    /**
     * Set writtenDate.
     */
    public function setWrittenDate(string $writtenDate) : self {
        $this->writtenDate = $writtenDate;

        return $this;
    }

    /**
     * Get writtenDate.
     */
    public function getWrittenDate() : string {
        return $this->writtenDate;
    }

    /**
     * Set date.
     */
    public function setDate(string $date) : self {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     */
    public function getDate() : string {
        return $this->date;
    }

    /**
     * Set transcription.
     */
    public function setTranscription(string $transcription) : self {
        $this->transcription = $transcription;

        return $this;
    }

    /**
     * Get transcription.
     */
    public function getTranscription() : string {
        return $this->transcription;
    }

    /**
     * Set annotations.
     */
    public function setAnnotations(string $annotations) : self {
        $this->annotations = $annotations;

        return $this;
    }

    /**
     * Get annotations.
     */
    public function getAnnotations() : string {
        return $this->annotations;
    }

    /**
     * Set category.
     */
    public function setCategory(Category $category) : self {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category.
     */
    public function getCategory() : Category {
        return $this->category;
    }

    /**
     * Set source.
     */
    public function setSource(Source $source) : self {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source.
     */
    public function getSource() : Source {
        return $this->source;
    }

    /**
     * Set thumbnailPath.
     */
    public function setThumbnailPath(string $thumbnailPath) : self {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

    /**
     * Get thumbnailPath.
     */
    public function getThumbnailPath() : string {
        return $this->thumbnailPath;
    }
}

<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Clipping
 *
 * @ORM\Table(name="clipping", indexes={
 *  @ORM\Index(columns="transcription", flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClippingRepository")
 */
class Clipping extends AbstractEntity {

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $originalName;

    /**
     * @var File
     */
    private $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=48, nullable=false)
     */
    private $imageFilePath;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=48, nullable=false)
     */
    private $thumbnailPath;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $imageSize;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $imageWidth;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $imageHeight;

    /**
     * @var string
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $number;

    /**
     * @var string
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $writtenDate;

    /**
     * YYYY-MM-DD
     *
     * @var string
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Regex(pattern="/\d{4}-\d{2}-\d{2}/", message="The format must be YYYY-MM-DD. The year must be four digits. Month and day must be two digits.")
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $transcription;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $annotations;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="clippings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var Source
     * @ORM\ManyToOne(targetEntity="Source", inversedBy="clippings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $source;

    public function __toString() {
        return $this->originalName;
    }

    /**
     * Set originalName
     *
     * @param string $originalName
     *
     * @return Clipping
     */
    public function setOriginalName($originalName) {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get originalName
     *
     * @return string
     */
    public function getOriginalName() {
        return $this->originalName;
    }

    /**
     * Get the image file
     *
     * @return File
     */
    public function getImageFile() {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile) {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * Set imageFilePath
     *
     * @param string $imageFilePath
     *
     * @return Clipping
     */
    public function setImageFilePath($imageFilePath) {
        $this->imageFilePath = $imageFilePath;

        return $this;
    }

    /**
     * Get imageFilePath
     *
     * @return string
     */
    public function getImageFilePath() {
        return $this->imageFilePath;
    }

    /**
     * Set imageSize
     *
     * @param integer $imageSize
     *
     * @return Clipping
     */
    public function setImageSize($imageSize) {
        $this->imageSize = $imageSize;

        return $this;
    }

    /**
     * Get imageSize
     *
     * @return integer
     */
    public function getImageSize() {
        return $this->imageSize;
    }

    /**
     * Set imageWidth
     *
     * @param integer $imageWidth
     *
     * @return Clipping
     */
    public function setImageWidth($imageWidth) {
        $this->imageWidth = $imageWidth;

        return $this;
    }

    /**
     * Get imageWidth
     *
     * @return integer
     */
    public function getImageWidth() {
        return $this->imageWidth;
    }

    /**
     * Set imageHeight
     *
     * @param integer $imageHeight
     *
     * @return Clipping
     */
    public function setImageHeight($imageHeight) {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    /**
     * Get imageHeight
     *
     * @return integer
     */
    public function getImageHeight() {
        return $this->imageHeight;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Clipping
     */
    public function setNumber($number) {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * Set writtenDate
     *
     * @param string $writtenDate
     *
     * @return Clipping
     */
    public function setWrittenDate($writtenDate) {
        $this->writtenDate = $writtenDate;

        return $this;
    }

    /**
     * Get writtenDate
     *
     * @return string
     */
    public function getWrittenDate() {
        return $this->writtenDate;
    }

    /**
     * Set date
     *
     * @param string $date
     *
     * @return Clipping
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set transcription
     *
     * @param string $transcription
     *
     * @return Clipping
     */
    public function setTranscription($transcription) {
        $this->transcription = $transcription;

        return $this;
    }

    /**
     * Get transcription
     *
     * @return string
     */
    public function getTranscription() {
        return $this->transcription;
    }

    /**
     * Set annotations
     *
     * @param string $annotations
     *
     * @return Clipping
     */
    public function setAnnotations($annotations) {
        $this->annotations = $annotations;

        return $this;
    }

    /**
     * Get annotations
     *
     * @return string
     */
    public function getAnnotations() {
        return $this->annotations;
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return Clipping
     */
    public function setCategory(Category $category) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set source
     *
     * @param Source $source
     *
     * @return Clipping
     */
    public function setSource(Source $source) {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return Source
     */
    public function getSource() {
        return $this->source;
    }


    /**
     * Set thumbnailPath
     *
     * @param string $thumbnailPath
     *
     * @return Clipping
     */
    public function setThumbnailPath($thumbnailPath)
    {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

    /**
     * Get thumbnailPath
     *
     * @return string
     */
    public function getThumbnailPath()
    {
        return $this->thumbnailPath;
    }
}

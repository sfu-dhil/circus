<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ClippingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'clipping')]
#[ORM\Entity(repositoryClass: ClippingRepository::class)]
#[ORM\Index(columns: ['transcription'], flags: ['fulltext'])]
class Clipping extends AbstractEntity {
    #[ORM\Column(type: Types::STRING, length: 128, nullable: false)]
    private ?string $originalName = null;

    private ?File $imageFile = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private ?string $imageFilePath = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private ?string $thumbnailPath = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private ?int $imageSize = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $imageWidth = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $imageHeight = null;

    #[ORM\Column(type: Types::STRING, length: 24, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(type: Types::STRING, length: 24, nullable: true)]
    private ?string $writtenDate = null;

    /**
     * YYYY-MM-DD.
     */
    #[Assert\Regex(pattern: '/\d{4}-\d{2}-\d{2}/', message: 'The format must be YYYY-MM-DD. The year must be four digits. Month and day must be two digits.')]
    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    private ?string $date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $transcription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $annotations = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'clippings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: Source::class, inversedBy: 'clippings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Source $source = null;

    public function __toString() : string {
        return $this->originalName ?? '';
    }

    public function setOriginalName(string $originalName) : self {
        $this->originalName = $originalName;

        return $this;
    }

    public function getOriginalName() : ?string {
        return $this->originalName;
    }

    public function getImageFile() : ?File {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile) : self {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function setImageFilePath(string $imageFilePath) : self {
        $this->imageFilePath = $imageFilePath;

        return $this;
    }

    public function getImageFilePath() : ?string {
        return $this->imageFilePath;
    }

    public function setImageSize(int $imageSize) : self {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getImageSize() : ?int {
        return $this->imageSize;
    }

    public function setImageWidth(int $imageWidth) : self {
        $this->imageWidth = $imageWidth;

        return $this;
    }

    public function getImageWidth() : ?int {
        return $this->imageWidth;
    }

    public function setImageHeight(int $imageHeight) : self {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    public function getImageHeight() : ?int {
        return $this->imageHeight;
    }

    public function setNumber(string $number) : self {
        $this->number = $number;

        return $this;
    }

    public function getNumber() : ?string {
        return $this->number;
    }

    public function setWrittenDate(string $writtenDate) : self {
        $this->writtenDate = $writtenDate;

        return $this;
    }

    public function getWrittenDate() : ?string {
        return $this->writtenDate;
    }

    public function setDate(string $date) : self {
        $this->date = $date;

        return $this;
    }

    public function getDate() : ?string {
        return $this->date;
    }

    public function setTranscription(string $transcription) : self {
        $this->transcription = $transcription;

        return $this;
    }

    public function getTranscription() : ?string {
        return $this->transcription;
    }

    public function setAnnotations(string $annotations) : self {
        $this->annotations = $annotations;

        return $this;
    }

    public function getAnnotations() : ?string {
        return $this->annotations;
    }

    public function setCategory(Category $category) : self {
        $this->category = $category;

        return $this;
    }

    public function getCategory() : ?Category {
        return $this->category;
    }

    public function setSource(Source $source) : self {
        $this->source = $source;

        return $this;
    }

    public function getSource() : ?Source {
        return $this->source;
    }

    public function setThumbnailPath(string $thumbnailPath) : self {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

    public function getThumbnailPath() : ?string {
        return $this->thumbnailPath;
    }
}

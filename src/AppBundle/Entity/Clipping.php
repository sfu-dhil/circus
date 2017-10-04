<?php

namespace AppBundle\Entity;

use AppBundle\Repository\ClippingRepository;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Clipping
 *
 * @ORM\Table(name="clipping")
 * @ORM\Entity(repositoryClass="ClippingRepository")
 */
class Clipping extends AbstractEntity
{
    /**
     * @var string
     * @ORM\Column(type="string", length=24, nullable=false)
     */
    private $imageNumber;
    
    /**
     * @var File
     * @ORM\Column(type="string", length=48, nullable=false)
     * @Assert\NotBlank(message="Upload the clipping image.")
     * @Assert\Image()
     */
    private $imageFile;

    /**
     * @var string
     * @ORM\Column(type="string", length=24, nullable=false)
     */    
    private $number;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=24, nullable=false)
     */
    private $writtenDate;
    
    /**
     * YYYY-MM-DD
     * 
     * @var string
     * @ORM\Column(type="string", length=10, nullable=false)
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
        return $this->imageNumber;
    }


    /**
     * Set imageNumber
     *
     * @param string $imageNumber
     *
     * @return Clipping
     */
    public function setImageNumber($imageNumber)
    {
        $this->imageNumber = $imageNumber;

        return $this;
    }

    /**
     * Get imageNumber
     *
     * @return string
     */
    public function getImageNumber()
    {
        return $this->imageNumber;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Clipping
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set writtenDate
     *
     * @param string $writtenDate
     *
     * @return Clipping
     */
    public function setWrittenDate($writtenDate)
    {
        $this->writtenDate = $writtenDate;

        return $this;
    }

    /**
     * Get writtenDate
     *
     * @return string
     */
    public function getWrittenDate()
    {
        return $this->writtenDate;
    }

    /**
     * Set date
     *
     * @param string $date
     *
     * @return Clipping
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set transcription
     *
     * @param string $transcription
     *
     * @return Clipping
     */
    public function setTranscription($transcription)
    {
        $this->transcription = $transcription;

        return $this;
    }

    /**
     * Get transcription
     *
     * @return string
     */
    public function getTranscription()
    {
        return $this->transcription;
    }

    /**
     * Set annotations
     *
     * @param string $annotations
     *
     * @return Clipping
     */
    public function setAnnotations($annotations)
    {
        $this->annotations = $annotations;

        return $this;
    }

    /**
     * Get annotations
     *
     * @return string
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return Clipping
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set source
     *
     * @param Source $source
     *
     * @return Clipping
     */
    public function setSource(Source $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return Source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set imageFile
     *
     * @param File $imageFile
     *
     * @return Clipping
     */
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * Get imageFile
     *
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

#[ORM\Table(name: 'source')]
#[ORM\Entity(repositoryClass: SourceRepository::class)]
class Source extends AbstractTerm {
    /**
     * YYYY-MM-DD.
     */
    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    private ?string $date = null;

    /**
     * @var Clipping[]|Collection
     */
    #[ORM\OneToMany(targetEntity: Clipping::class, mappedBy: 'source')]
    private Collection $clippings;

    public function __construct() {
        parent::__construct();
        $this->clippings = new ArrayCollection();
    }

    public function setDate(string $date) : self {
        $this->date = $date;

        return $this;
    }

    public function getDate() : ?string {
        return $this->date;
    }

    public function addClipping(Clipping $clipping) : self {
        $this->clippings[] = $clipping;

        return $this;
    }

    public function removeClipping(Clipping $clipping) : void {
        $this->clippings->removeElement($clipping);
    }

    public function getClippings() : Collection {
        return $this->clippings;
    }
}

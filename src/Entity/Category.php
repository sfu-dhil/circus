<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

#[ORM\Table(name: 'category')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends AbstractTerm {
    /**
     * @var Clipping[]|Collection
     */
    #[ORM\OneToMany(targetEntity: Clipping::class, mappedBy: 'category')]
    private Collection $clippings;

    public function __construct() {
        parent::__construct();
        $this->clippings = new ArrayCollection();
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

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category extends AbstractTerm
{    
    /**
     * @var Collection|Clipping[]
     * @ORM\OneToMany(targetEntity="Clipping", mappedBy="category")
     */
    private $clippings;

    /**
     * Add clipping
     *
     * @param \AppBundle\Entity\Clipping $clipping
     *
     * @return Category
     */
    public function addClipping(\AppBundle\Entity\Clipping $clipping)
    {
        $this->clippings[] = $clipping;

        return $this;
    }

    /**
     * Remove clipping
     *
     * @param \AppBundle\Entity\Clipping $clipping
     */
    public function removeClipping(\AppBundle\Entity\Clipping $clipping)
    {
        $this->clippings->removeElement($clipping);
    }

    /**
     * Get clippings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClippings()
    {
        return $this->clippings;
    }
}

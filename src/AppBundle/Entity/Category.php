<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Category.
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category extends AbstractTerm {
    /**
     * @var Clipping[]|Collection
     * @ORM\OneToMany(targetEntity="Clipping", mappedBy="category")
     */
    private $clippings;

    /**
     * Add clipping.
     *
     * @param \AppBundle\Entity\Clipping $clipping
     *
     * @return Category
     */
    public function addClipping(Clipping $clipping) {
        $this->clippings[] = $clipping;

        return $this;
    }

    /**
     * Remove clipping.
     *
     * @param \AppBundle\Entity\Clipping $clipping
     */
    public function removeClipping(Clipping $clipping) : void {
        $this->clippings->removeElement($clipping);
    }

    /**
     * Get clippings.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClippings() {
        return $this->clippings;
    }
}

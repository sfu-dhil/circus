<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Category.
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category extends AbstractTerm {
    /**
     * @var Clipping[]|Collection
     * @ORM\OneToMany(targetEntity="Clipping", mappedBy="source")
     */
    private $clippings;

    public function __construct() {
        parent::__construct();
        $this->clippings = new ArrayCollection();
    }

    /**
     * Add clipping.
     *
     * @param \App\Entity\Clipping $clipping
     */
    public function addClipping(Clipping $clipping) : self {
        $this->clippings[] = $clipping;

        return $this;
    }

    /**
     * Remove clipping.
     *
     * @param \App\Entity\Clipping $clipping
     */
    public function removeClipping(Clipping $clipping) : void {
        $this->clippings->removeElement($clipping);
    }

    /**
     * Get clippings.
     */
    public function getClippings() : Collection {
        return $this->clippings;
    }
}

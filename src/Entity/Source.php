<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Source.
 *
 * @ORM\Table(name="source")
 * @ORM\Entity(repositoryClass="App\Repository\SourceRepository")
 */
class Source extends AbstractTerm {
    /**
     * YYYY-MM-DD.
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private string $date;

    /**
     * @var Clipping[]|Collection
     * @ORM\OneToMany(targetEntity="Clipping", mappedBy="source")
     */
    private $clippings;

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
    public function getClippings() : \Doctrine\Common\Collections\Collection {
        return $this->clippings;
    }
}

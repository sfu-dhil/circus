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
 * Source.
 *
 * @ORM\Table(name="source")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SourceRepository")
 */
class Source extends AbstractTerm {
    /**
     * YYYY-MM-DD.
     *
     * @var string
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $date;

    /**
     * @var Clipping[]|Collection
     * @ORM\OneToMany(targetEntity="Clipping", mappedBy="source")
     */
    private $clippings;

    /**
     * Set date.
     *
     * @param string $date
     *
     * @return Source
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return string
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Add clipping.
     *
     * @param \AppBundle\Entity\Clipping $clipping
     *
     * @return Source
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

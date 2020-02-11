<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Source;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadSource form.
 */
class LoadSource extends Fixture {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Source();
            $fixture->setName('source-' . $i);
            $fixture->setLabel('Source ' . $i);
            $fixture->setDescription("This is test source #{$i}");
            $fixture->setDate(1900 + $i);

            $em->persist($fixture);
            $this->setReference('source.' . $i, $fixture);
        }

        $em->flush();
    }
}

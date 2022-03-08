<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Source;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class SourceFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) : void {
        for ($i = 1; $i <= 5; $i++) {
            $fixture = new Source();
            $fixture->setLabel('Label ' . $i);
            $fixture->setDescription("<p>This is paragraph {$i}</p>");
            $fixture->setDate("1850-0{$i}-02");

            $manager->persist($fixture);
            $this->setReference('source.' . $i, $fixture);
        }
        $manager->flush();
    }
}

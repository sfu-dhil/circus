<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Source;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadSource form.
 */
class SourceFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Source();
            $fixture->setName('source-' . $i);
            $fixture->setLabel('Source ' . $i);
            $fixture->setDescription("This is test source #{$i}");
            $fixture->setDate(sprintf('%d', 1900 + $i));

            $manager->persist($fixture);
            $this->setReference('source.' . $i, $fixture);
        }

        $manager->flush();
    }
}

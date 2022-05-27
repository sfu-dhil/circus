<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadCategory form.
 */
class CategoryFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Category();
            $fixture->setName('category-' . $i);
            $fixture->setLabel('Category ' . $i);
            $fixture->setDescription("This is test category #{$i}");

            $manager->persist($fixture);
            $this->setReference('category.' . $i, $fixture);
        }

        $manager->flush();
    }
}

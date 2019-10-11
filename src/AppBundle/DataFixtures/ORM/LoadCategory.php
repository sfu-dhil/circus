<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadCategory form.
 */
class LoadCategory extends Fixture {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Category();
            $fixture->setName('category-' . $i);
            $fixture->setLabel('Category ' . $i);
            $fixture->setDescription("This is test category #{$i}");

            $em->persist($fixture);
            $this->setReference('category.' . $i, $fixture);
        }

        $em->flush();
    }
}

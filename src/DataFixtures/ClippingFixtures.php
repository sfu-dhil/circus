<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Clipping;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ClippingFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) : void {
        for ($i = 1; $i <= 5; $i++) {
            $fixture = new Clipping();
            $fixture->setOriginalName('OriginalName ' . $i);
            $fixture->setImageFilePath('ImageFilePath ' . $i);
            $fixture->setThumbnailPath('ThumbnailPath ' . $i);
            $fixture->setImageSize($i);
            $fixture->setImageWidth($i);
            $fixture->setImageHeight($i);
            $fixture->setNumber('Number ' . $i);
            $fixture->setWrittenDate('WrittenDate ' . $i);
            $fixture->setDate("1850-0{$i}-02");
            $fixture->setTranscription("<p>This is paragraph {$i}</p>");
            $fixture->setAnnotations("<p>This is paragraph {$i}</p>");
            $fixture->setCategory($this->getReference('category.' . $i));
            $fixture->setSource($this->getReference('source.' . $i));
            $manager->persist($fixture);
            $this->setReference('clipping.' . $i, $fixture);
        }
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string>
     */
    public function getDependencies() : array {
        return [
            CategoryFixtures::class,
            SourceFixtures::class,
        ];
    }
}

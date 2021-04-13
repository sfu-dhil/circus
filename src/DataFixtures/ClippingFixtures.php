<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Clipping;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadClipping form.
 */
class ClippingFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Clipping();
            $fixture->setOriginalName('OriginalName ' . $i);
            $fixture->setImageFilePath('ImageFilePath ' . $i);
            $fixture->setThumbnailPath('ThumbnailPath ' . $i);
            $fixture->setImageSize(16000);
            $fixture->setImageWidth(400);
            $fixture->setImageHeight(400);
            $fixture->setNumber('Number ' . $i);
            $fixture->setWrittenDate('WrittenDate ' . $i);
            $fixture->setDate(1850 + $i);
            $fixture->setTranscription('Transcription ' . $i);
            $fixture->setAnnotations('Annotations ' . $i);
            $fixture->setCategory($this->getReference('category.1'));
            $fixture->setSource($this->getReference('source.1'));

            $em->persist($fixture);
            $this->setReference('clipping.' . $i, $fixture);
        }

        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies() {
        // add dependencies here, or remove this
        // function and "implements DependentFixtureInterface" above
        return [
            CategoryFixtures::class,
            SourceFixtures::class,
        ];
    }
}

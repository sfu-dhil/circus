<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Clipping;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadClipping form.
 */
class LoadClipping extends Fixture implements DependentFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) {
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
            LoadCategory::class,
            LoadSource::class,
        ];
    }

}

<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\Repository\ClippingRepository;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\TestCase\ControllerTestCase;
use Symfony\Component\HttpFoundation\Response;

class ClippingTest extends ControllerTestCase {
    // Change this to HTTP_OK when the site is public.
    private const ANON_RESPONSE_CODE = Response::HTTP_OK;

    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/clipping/');
        $this->assertResponseStatusCodeSame(self::ANON_RESPONSE_CODE);
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/clipping/');
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/clipping/');
        $this->assertResponseIsSuccessful();
        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/clipping/1');
        $this->assertResponseStatusCodeSame(self::ANON_RESPONSE_CODE);
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
    }

    public function testUserShow() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/clipping/1');
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $crawler->selectLink('Edit')->count());
    }

    public function testAdminShow() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/clipping/1');
        $this->assertResponseIsSuccessful();
        $this->assertSame(1, $crawler->selectLink('Edit')->count());
    }

    public function testAnonSearch() : void {
        $crawler = $this->client->request('GET', '/clipping/search');
        $this->assertResponseStatusCodeSame(self::ANON_RESPONSE_CODE);
        if (self::ANON_RESPONSE_CODE === Response::HTTP_FOUND) {
            // If authentication is required stop here.
            return;
        }

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'clipping',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUserSearch() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/clipping/search');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'clipping',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminSearch() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/clipping/search');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('btn-search')->form([
            'q' => 'clipping',
        ]);

        $responseCrawler = $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/clipping/1/edit');
        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
    }

    public function testUserEdit() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/clipping/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/clipping/1/edit');
        $this->assertResponseIsSuccessful();

        $form = $formCrawler->selectButton('Update')->form([
            'clipping[originalName]' => 'Updated OriginalName',
            'clipping[imageFilePath]' => 'Updated ImageFilePath',
            'clipping[thumbnailPath]' => 'Updated ThumbnailPath',
            'clipping[imageSize]' => 10,
            'clipping[imageWidth]' => 10,
            'clipping[imageHeight]' => 10,
            'clipping[number]' => 'Updated Number',
            'clipping[writtenDate]' => 'Updated WrittenDate',
            'clipping[date]' => 'Updated Date',
            'clipping[transcription]' => '<p>Updated Text</p>',
            'clipping[annotations]' => '<p>Updated Text</p>',
        ]);
        $form['clipping[category]']->disableValidation()->setValue(2);
        $form['clipping[source]']->disableValidation()->setValue(2);

        $this->client->submit($form);
        $this->assertResponseRedirects('/clipping/1', Response::HTTP_FOUND);
        $responseCrawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/clipping/new');
        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
    }

    public function testUserNew() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request('GET', '/clipping/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request('GET', '/clipping/new');
        $this->assertResponseIsSuccessful();

        $form = $formCrawler->selectButton('Create')->form([
            'clipping[originalName]' => 'Updated OriginalName',
            'clipping[imageFilePath]' => 'Updated ImageFilePath',
            'clipping[thumbnailPath]' => 'Updated ThumbnailPath',
            'clipping[imageSize]' => 10,
            'clipping[imageWidth]' => 10,
            'clipping[imageHeight]' => 10,
            'clipping[number]' => 'Updated Number',
            'clipping[writtenDate]' => 'Updated WrittenDate',
            'clipping[date]' => 'Updated Date',
            'clipping[transcription]' => '<p>Updated Text</p>',
            'clipping[annotations]' => '<p>Updated Text</p>',
        ]);
        $form['clipping[category]']->disableValidation()->setValue(2);
        $form['clipping[source]']->disableValidation()->setValue(2);

        $this->client->submit($form);
        $this->assertResponseRedirects('/clipping/6', Response::HTTP_FOUND);
        $responseCrawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testAdminDelete() : void {
        /** @var ClippingRepository $repo */
        $repo = self::$container->get(ClippingRepository::class);
        $preCount = count($repo->findAll());

        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request('GET', '/clipping/1');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Delete')->form();
        $this->client->submit($form);

        $this->assertResponseRedirects('/clipping/', Response::HTTP_FOUND);
        $responseCrawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->em->clear();
        $postCount = count($repo->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}

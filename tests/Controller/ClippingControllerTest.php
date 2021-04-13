<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use App\DataFixtures\ClippingFixtures;
use App\Entity\Clipping;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\Tests\ControllerBaseCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ClippingControllerTest extends ControllerBaseCase
{
    protected function fixtures() : array {
        return [
            UserFixtures::class,
            ClippingFixtures::class,
        ];
    }

    public function testAnonIndex() : void {
        $crawler = $this->client->request('GET', '/clipping/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/clipping/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/clipping/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $crawler = $this->client->request('GET', '/clipping/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/clipping/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/clipping/1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request('GET', '/clipping/1/edit');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserEdit() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/clipping/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/clipping/1/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $path = dirname(__FILE__, 2) . '/data/image.jpg';
        $form = $formCrawler->selectButton('Update')->form([
            'clipping[number]' => '47',
            'clipping[writtenDate]' => 'April 1972',
            'clipping[date]' => '1792-09-13',
            'clipping[category]' => 1,
            'clipping[source]' => 1,
            'clipping[transcription]' => 'It is a circus',
            'clipping[annotations]' => 'Circus photo',
        ]);
        $form['clipping[newImageFile]']->upload($path);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect('/clipping/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(4, $responseCrawler->filter('div:contains("April 1972")')->count());
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request('GET', '/clipping/new');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserNew() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/clipping/new');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminNew() : void {
        $this->login('user.admin');
        $formCrawler = $this->client->request('GET', '/clipping/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $image = new UploadedFile(dirname(__FILE__, 2) . '/data/image.jpg', 'image.jpg', 'image/jpeg', 123);
        $form = $formCrawler->selectButton('Create')->form([
            'clipping[imageFile]' => $image,
            'clipping[number]' => '47',
            'clipping[writtenDate]' => 'April 1972',
            'clipping[date]' => '1792-09-13',
            'clipping[category]' => 1,
            'clipping[source]' => 1,
            'clipping[transcription]' => 'It is a circus',
            'clipping[annotations]' => 'Circus photo',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(4, $responseCrawler->filter('div:contains("April 1972")')->count());
    }

    public function testAnonDelete() : void {
        $crawler = $this->client->request('GET', '/clipping/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserDelete() : void {
        $this->login('user.user');
        $crawler = $this->client->request('GET', '/clipping/1/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() : void {
        $preCount = count($this->entityManager->getRepository(Clipping::class)->findAll());
        $this->login('user.admin');
        $crawler = $this->client->request('GET', '/clipping/1/delete');
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->entityManager->clear();
        $postCount = count($this->entityManager->getRepository(Clipping::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}

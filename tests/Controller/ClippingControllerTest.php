<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Clipping;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\TestCase\ControllerTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClippingControllerTest extends ControllerTestCase {
    public function testAnonIndex() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/1/edit');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserEdit() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/1/edit');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
    }

    public function testAdminEdit() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request(Request::METHOD_GET, '/clipping/1/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

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
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(4, $responseCrawler->filter('div:contains("April 1972")')->count());
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/new');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserNew() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/new');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
    }

    public function testAdminNew() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request(Request::METHOD_GET, '/clipping/new');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

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
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(4, $responseCrawler->filter('div:contains("April 1972")')->count());
    }

    public function testAnonDelete() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/1/delete');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserDelete() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/1/delete');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
    }

    public function testAdminDelete() : void {
        $preCount = count($this->em->getRepository(Clipping::class)->findAll());
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request(Request::METHOD_GET, '/clipping/1/delete');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->em->clear();
        $postCount = count($this->em->getRepository(Clipping::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}

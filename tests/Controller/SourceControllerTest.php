<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Source;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\TestCase\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SourceControllerTest extends ControllerTestCase {
    public function testAnonIndex() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/source/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/source/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request(Request::METHOD_GET, '/source/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/source/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/source/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request(Request::METHOD_GET, '/source/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/source/1/edit');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserEdit() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/source/1/edit');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
    }

    public function testAdminEdit() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request(Request::METHOD_GET, '/source/1/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $form = $formCrawler->selectButton('Update')->form([
            'source[name]' => 'Cheese.',
            'source[label]' => 'Cheese',
            'source[description]' => 'It is a cheese.',
            'source[date]' => 'April 1972',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect('/source/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(1, $responseCrawler->filter('td:contains("It is a cheese.")')->count());
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/source/new');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserNew() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/source/new');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
    }

    public function testAdminNew() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request(Request::METHOD_GET, '/source/new');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $form = $formCrawler->selectButton('Create')->form([
            'source[name]' => 'Cheese.',
            'source[label]' => 'Cheese',
            'source[description]' => 'It is a cheese.',
            'source[date]' => 'April 1972',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(1, $responseCrawler->filter('td:contains("It is a cheese.")')->count());
    }

    public function testAnonDelete() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/source/1/delete');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserDelete() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/source/1/delete');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
    }

    public function testAdminDelete() : void {
        $preCount = count($this->em->getRepository(Source::class)->findAll());
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request(Request::METHOD_GET, '/source/1/delete');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->em->clear();
        $postCount = count($this->em->getRepository(Source::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}

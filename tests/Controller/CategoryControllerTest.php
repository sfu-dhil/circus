<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Category;
use Nines\UserBundle\DataFixtures\UserFixtures;
use Nines\UtilBundle\TestCase\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends ControllerTestCase {
    public function testAnonIndex() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/category/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/category/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request(Request::METHOD_GET, '/category/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/category/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/category/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request(Request::METHOD_GET, '/category/1');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/category/1/edit');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserEdit() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/category/1/edit');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
    }

    public function testAdminEdit() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request(Request::METHOD_GET, '/category/1/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $form = $formCrawler->selectButton('Update')->form([
            'category[name]' => 'Cheese.',
            'category[label]' => 'Cheese',
            'category[description]' => 'It is a cheese.',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect('/category/1'));
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(1, $responseCrawler->filter('td:contains("It is a cheese.")')->count());
    }

    public function testAnonNew() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/category/new');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserNew() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/category/new');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
    }

    public function testAdminNew() : void {
        $this->login(UserFixtures::ADMIN);
        $formCrawler = $this->client->request(Request::METHOD_GET, '/category/new');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $form = $formCrawler->selectButton('Create')->form([
            'category[name]' => 'Cheese.',
            'category[label]' => 'Cheese',
            'category[description]' => 'It is a cheese.',
        ]);

        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertSame(1, $responseCrawler->filter('td:contains("It is a cheese.")')->count());
    }

    public function testAnonDelete() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/category/1/delete');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testUserDelete() : void {
        $this->login(UserFixtures::USER);
        $crawler = $this->client->request(Request::METHOD_GET, '/category/1/delete');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
    }

    public function testAdminDelete() : void {
        $preCount = count($this->em->getRepository(Category::class)->findAll());
        $this->login(UserFixtures::ADMIN);
        $crawler = $this->client->request(Request::METHOD_GET, '/category/1/delete');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $responseCrawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->em->clear();
        $postCount = count($this->em->getRepository(Category::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}

<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Category;
use AppBundle\DataFixtures\ORM\LoadCategory;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class CategoryControllerTest extends BaseTestCase
{

    protected function getFixtures() {
        return [
            LoadUser::class,
            LoadCategory::class
        ];
    }

    public function testAnonIndex() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/category/');
        $this->assertNoCookies($client);
        $this->assertStatusCode(200, $client);
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/category/');
        $this->assertStatusCode(200, $client);

        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/category/');
        $this->assertStatusCode(200, $client);

        $this->assertEquals(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/category/1');
        $this->assertStatusCode(200, $client);
        $this->assertNoCookies($client);

        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/category/1');
        $this->assertStatusCode(200, $client);

        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/category/1');
        $this->assertStatusCode(200, $client);

        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(1, $crawler->selectLink('Delete')->count());
    }
    public function testAnonEdit() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/category/1/edit');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testUserEdit() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/category/1/edit');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAdminEdit() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $formCrawler = $client->request('GET', '/category/1/edit');
        $this->assertStatusCode(200, $client);


        $form = $formCrawler->selectButton('Update')->form([
            'category[name]' => 'Cheese.',
            'category[label]' => 'Cheese',
            'category[description]' => 'It is a cheese.'
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/category/1'));
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);

        $this->assertEquals(1, $responseCrawler->filter('td:contains("Cheese.")')->count());
    }

    public function testAnonNew() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/category/new');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testUserNew() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/category/new');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAdminNew() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $formCrawler = $client->request('GET', '/category/new');
        $this->assertStatusCode(200, $client);


        $form = $formCrawler->selectButton('Create')->form([
            'category[name]' => 'Cheese.',
            'category[label]' => 'Cheese',
            'category[description]' => 'It is a cheese.'
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);

        $this->assertEquals(1, $responseCrawler->filter('td:contains("Cheese.")')->count());
    }

    public function testAnonDelete() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/category/1/delete');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testUserDelete() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/category/1/delete');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAdminDelete() {
        self::bootKernel();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $preCount = count($em->getRepository(Category::class)->findAll());
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/category/1/delete');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);


        $em->clear();
        $postCount = count($em->getRepository(Category::class)->findAll());
        $this->assertEquals($preCount - 1, $postCount);
    }

}

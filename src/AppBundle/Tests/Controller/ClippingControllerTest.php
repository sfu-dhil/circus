<?php

namespace AppBundle\Tests\Controller;

use AppBundle\DataFixtures\ORM\LoadClipping;
use AppBundle\Entity\Clipping;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ClippingControllerTest extends BaseTestCase
{

    protected function getFixtures() {
        return [
            LoadUser::class,
            LoadClipping::class
        ];
    }

    public function testAnonIndex() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/clipping/');
        $this->assertStatusCode(200, $client);
        $this->assertNoCookies($client);

        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/clipping/');
        $this->assertStatusCode(200, $client);

        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/clipping/');
        $this->assertStatusCode(200, $client);

        $this->assertEquals(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/clipping/1');
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
        $crawler = $client->request('GET', '/clipping/1');
        $this->assertStatusCode(200, $client);

        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/clipping/1');
        $this->assertStatusCode(200, $client);

        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(1, $crawler->selectLink('Delete')->count());
    }
    public function testAnonEdit() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/clipping/1/edit');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testUserEdit() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/clipping/1/edit');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAdminEdit() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $formCrawler = $client->request('GET', '/clipping/1/edit');
        $this->assertStatusCode(200, $client);


        $image = new UploadedFile(dirname(dirname(__FILE__)) . '/data/image.jpg', 'image.jpg', 'image/jpeg', 123);
        $form = $formCrawler->selectButton('Update')->form([
            'clipping[newImageFile]' => $image,
            'clipping[number]' => '47',
            'clipping[writtenDate]' => 'April 1972',
            'clipping[date]' => '1792-09-13',
            'clipping[category]' => 1,
            'clipping[source]' => 1,
            'clipping[transcription]' => 'It is a circus',
            'clipping[annotations]' => 'Circus photo'
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/clipping/1'));
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);

        $this->assertEquals(1, $responseCrawler->filter('td:contains("April 1972")')->count());
    }

    public function testAnonNew() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/clipping/new');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testUserNew() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/clipping/new');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAdminNew() {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $formCrawler = $client->request('GET', '/clipping/new');
        $this->assertStatusCode(200, $client);


        $image = new UploadedFile(dirname(dirname(__FILE__)) . '/data/image.jpg', 'image.jpg', 'image/jpeg', 123);
        $form = $formCrawler->selectButton('Create')->form([
            'clipping[imageFile]' => $image,
            'clipping[number]' => '47',
            'clipping[writtenDate]' => 'April 1972',
            'clipping[date]' => '1792-09-13',
            'clipping[category]' => 1,
            'clipping[source]' => 1,
            'clipping[transcription]' => 'It is a circus',
            'clipping[annotations]' => 'Circus photo'
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);

        $this->assertEquals(1, $responseCrawler->filter('td:contains("April 1972")')->count());
    }

    public function testAnonDelete() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/clipping/1/delete');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testUserDelete() {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/clipping/1/delete');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAdminDelete() {
        self::bootKernel();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $preCount = count($em->getRepository(Clipping::class)->findAll());
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/clipping/1/delete');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);


        $em->clear();
        $postCount = count($em->getRepository(Clipping::class)->findAll());
        $this->assertEquals($preCount - 1, $postCount);
    }

}

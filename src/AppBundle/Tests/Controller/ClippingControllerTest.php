<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Tests\Controller;

use AppBundle\DataFixtures\ORM\LoadClipping;
use AppBundle\Entity\Clipping;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ClippingControllerTest extends BaseTestCase {
    protected function getFixtures() {
        return [
            LoadUser::class,
            LoadClipping::class,
        ];
    }

    public function testAnonIndex() : void {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/clipping/');
        $this->assertStatusCode(200, $client);
        $this->assertNoCookies($client);

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/clipping/');
        $this->assertStatusCode(200, $client);

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/clipping/');
        $this->assertStatusCode(200, $client);

        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/clipping/1');
        $this->assertStatusCode(200, $client);
        $this->assertNoCookies($client);

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testUserShow() : void {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/clipping/1');
        $this->assertStatusCode(200, $client);

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/clipping/1');
        $this->assertStatusCode(200, $client);

        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/clipping/1/edit');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testUserEdit() : void {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/clipping/1/edit');
        $this->assertStatusCode(403, $client);
    }

    public function testAdminEdit() : void {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $formCrawler = $client->request('GET', '/clipping/1/edit');
        $this->assertStatusCode(200, $client);

        $image = new UploadedFile(dirname(__FILE__, 2) . '/data/image.jpg', 'image.jpg', 'image/jpeg', 123);
        $form = $formCrawler->selectButton('Update')->form([
            'clipping[newImageFile]' => $image,
            'clipping[number]' => '47',
            'clipping[writtenDate]' => 'April 1972',
            'clipping[date]' => '1792-09-13',
            'clipping[category]' => 1,
            'clipping[source]' => 1,
            'clipping[transcription]' => 'It is a circus',
            'clipping[annotations]' => 'Circus photo',
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/clipping/1'));
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);

        $this->assertSame(1, $responseCrawler->filter('td:contains("April 1972")')->count());
    }

    public function testAnonNew() : void {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/clipping/new');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testUserNew() : void {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/clipping/new');
        $this->assertStatusCode(403, $client);
    }

    public function testAdminNew() : void {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $formCrawler = $client->request('GET', '/clipping/new');
        $this->assertStatusCode(200, $client);

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

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);

        $this->assertSame(1, $responseCrawler->filter('td:contains("April 1972")')->count());
    }

    public function testAnonDelete() : void {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/clipping/1/delete');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testUserDelete() : void {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/clipping/1/delete');
        $this->assertStatusCode(403, $client);
    }

    public function testAdminDelete() : void {
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
        $this->assertSame($preCount - 1, $postCount);
    }
}

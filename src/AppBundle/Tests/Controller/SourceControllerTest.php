<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Tests\Controller;

use AppBundle\DataFixtures\ORM\LoadSource;
use AppBundle\Entity\Source;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class SourceControllerTest extends BaseTestCase {
    protected function getFixtures() {
        return [
            LoadUser::class,
            LoadSource::class,
        ];
    }

    public function testAnonIndex() : void {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/source/');
        $this->assertStatusCode(200, $client);
        $this->assertNoCookies($client);

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testUserIndex() : void {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/source/');
        $this->assertStatusCode(200, $client);

        $this->assertSame(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() : void {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/source/');
        $this->assertStatusCode(200, $client);

        $this->assertSame(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() : void {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/source/1');
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
        $crawler = $client->request('GET', '/source/1');
        $this->assertStatusCode(200, $client);

        $this->assertSame(0, $crawler->selectLink('Edit')->count());
        $this->assertSame(0, $crawler->selectLink('Delete')->count());
    }

    public function testAdminShow() : void {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/source/1');
        $this->assertStatusCode(200, $client);

        $this->assertSame(1, $crawler->selectLink('Edit')->count());
        $this->assertSame(1, $crawler->selectLink('Delete')->count());
    }

    public function testAnonEdit() : void {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/source/1/edit');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testUserEdit() : void {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/source/1/edit');
        $this->assertStatusCode(403, $client);
    }

    public function testAdminEdit() : void {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $formCrawler = $client->request('GET', '/source/1/edit');
        $this->assertStatusCode(200, $client);

        $form = $formCrawler->selectButton('Update')->form([
            'source[name]' => 'Cheese.',
            'source[label]' => 'Cheese',
            'source[description]' => 'It is a cheese',
            'source[date]' => 'April 1972',
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/source/1'));
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);

        $this->assertSame(1, $responseCrawler->filter('td:contains("Cheese")')->count());
    }

    public function testAnonNew() : void {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/source/new');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testUserNew() : void {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/source/new');
        $this->assertStatusCode(403, $client);
    }

    public function testAdminNew() : void {
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $formCrawler = $client->request('GET', '/source/new');
        $this->assertStatusCode(200, $client);

        $form = $formCrawler->selectButton('Create')->form([
            'source[name]' => 'Cheese.',
            'source[label]' => 'Cheese',
            'source[description]' => 'It is a cheese',
            'source[date]' => 'April 1972',
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);

        $this->assertSame(1, $responseCrawler->filter('td:contains("Cheese")')->count());
    }

    public function testAnonDelete() : void {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/source/1/delete');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testUserDelete() : void {
        $client = $this->makeClient([
            'username' => 'user@example.com',
            'password' => 'secret',
        ]);
        $crawler = $client->request('GET', '/source/1/delete');
        $this->assertStatusCode(403, $client);
    }

    public function testAdminDelete() : void {
        self::bootKernel();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $preCount = count($em->getRepository(Source::class)->findAll());
        $client = $this->makeClient([
            'username' => 'admin@example.com',
            'password' => 'supersecret',
        ]);
        $crawler = $client->request('GET', '/source/1/delete');
        $this->assertStatusCode(302, $client);

        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertStatusCode(200, $client);

        $em->clear();
        $postCount = count($em->getRepository(Source::class)->findAll());
        $this->assertSame($preCount - 1, $postCount);
    }
}

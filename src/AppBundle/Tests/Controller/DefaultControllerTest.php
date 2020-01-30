<?php

namespace AppBundle\Tests\Controller;

use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class DefaultControllerTest extends BaseTestCase {
    public function getFixtures() {
        return array(
            LoadUser::class,
        );
    }

    public function testIndex() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertStatusCode(200, $client);
        $this->assertStringContainsString('Circus', $crawler->text());
        $this->assertNoCookies($client);
    }

    public function testLogin() {
        $client = static::createClient();
        $formCrawler = $client->request('GET', '/login');
        $this->assertStatusCode(200, $client);
        $this->assertCookieCount($client, 1);

        $form = $formCrawler->selectButton('Login')->form(array(
            '_username' => LoadUser::USER['username'],
            '_password' => LoadUser::USER['password'],
        ));
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();
        $this->assertStatusCode(200, $client);
        $this->assertCookieCount($client, 1);
    }
}

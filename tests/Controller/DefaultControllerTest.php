<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Nines\UtilBundle\TestCase\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends ControllerTestCase {
    public function testIndex() : void {
        $crawler = $this->client->request(Request::METHOD_GET, '/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Circus', $crawler->text(null, true));
    }
}

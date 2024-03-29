<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Controller;

use Nines\UtilBundle\TestCase\ControllerTestCase;

class DefaultControllerTest extends ControllerTestCase {
    public function testIndex() : void {
        $crawler = $this->client->request('GET', '/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Circus', $crawler->text(null, true));
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\SourceRepository;
use Nines\UtilBundle\TestCase\ServiceTestCase;

class SourceRepositoryTest extends ServiceTestCase {
    private const TYPEAHEAD_QUERY = 'source';

    private SourceRepository $repo;

    public function testSetUp() : void {
        $this->assertInstanceOf(SourceRepository::class, $this->repo);
    }

    public function testIndexQuery() : void {
        $query = $this->repo->indexQuery();
        $this->assertCount(5, $query->execute());
    }

    protected function setUp() : void {
        parent::setUp();
        $this->repo = self::$container->get(SourceRepository::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\CategoryRepository;
use Nines\UtilBundle\TestCase\ServiceTestCase;

class CategoryRepositoryTest extends ServiceTestCase {

    private CategoryRepository $repo;

    public function testSetUp() : void {
        $this->assertInstanceOf(CategoryRepository::class, $this->repo);
    }

    public function testIndexQuery() : void {
        $query = $this->repo->indexQuery();
        $this->assertCount(5, $query->execute());
    }

    protected function setUp() : void {
        parent::setUp();
        $this->repo = self::$container->get(CategoryRepository::class);
    }
}

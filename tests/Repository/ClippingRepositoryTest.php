<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\ClippingRepository;
use Nines\UtilBundle\TestCase\ServiceTestCase;

class ClippingRepositoryTest extends ServiceTestCase {
    private const TYPEAHEAD_QUERY = 'clipping';

    private ClippingRepository $repo;

    public function testSetUp() : void {
        $this->assertInstanceOf(ClippingRepository::class, $this->repo);
    }

    public function testIndexQuery() : void {
        $query = $this->repo->indexQuery();
        $this->assertCount(5, $query->execute());
    }

    /**
     * @dataProvider searchData
     */
    public function testSearchQuery($data, $count) : void {
        $query = $this->repo->searchQuery($data);
        $this->assertCount($count, $query->execute());
    }

    public function searchData() : array {
        return [
            [[], 5],
            [['transcription' => 'paragraph'], 5],
            [['transcription' => '"paragraph 2"'], 1],
            [['writtenDate' => 'WrittenDate 3'], 1],
            [['number' => 'Number 2'], 1],
            [['date' => '1850-02-02'], 1],
            [['source' => [1]], 1],
            [['source' => [1,3]], 2],
            [['category' => [1]], 1],
            [['category' => [1,3]], 2],
            [['order' => 'date'], 5],
            [['order' => 'number'], 5],
        ];
    }

    protected function setUp() : void {
        parent::setUp();
        $this->repo = self::$container->get(ClippingRepository::class);
    }
}

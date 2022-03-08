<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Source;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UtilBundle\Repository\TermRepository;

/**
 * @method null|Source find($id, $lockMode = null, $lockVersion = null)
 * @method Source[] findAll()
 * @method Source[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|Source findOneBy(array $criteria, array $orderBy = null)
 * @phpstan-extends ServiceEntityRepository<Source>
 */
class SourceRepository extends TermRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Source::class);
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('source')
            ->orderBy('source.id')
            ->getQuery();
    }
}

<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Clipping;
use App\Entity\Source;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Clipping find($id, $lockMode = null, $lockVersion = null)
 * @method Clipping[] findAll()
 * @method Clipping[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method null|Clipping findOneBy(array $criteria, array $orderBy = null)
 * @phpstan-extends ServiceEntityRepository<Clipping>
 */
class ClippingRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Clipping::class);
    }

    private function fulltextPart(QueryBuilder $qb, array $data, string $fieldName, string $formName) : void {
        if ( ! isset($data[$formName])) {
            return;
        }
        $term = trim($data[$formName]);
        if ( ! $term) {
            return;
        }

        $m = [];
        if (preg_match('/^"(.*)"$/u', $term, $m)) {
            $qb->andWhere("e.{$fieldName} like :{$fieldName}Exact");
            $qb->setParameter("{$fieldName}Exact", "%{$m[1]}%");
        } else {
            $qb->andWhere("MATCH (e.{$fieldName}) AGAINST(:{$fieldName} BOOLEAN) > 0");
            $qb->setParameter($fieldName, $term);
        }
    }

    /**
     * @param $data
     * @param $fieldName
     * @param $formName
     */
    private function textPart(QueryBuilder $qb, $data, $fieldName, $formName) : void {
        if ( ! isset($data[$formName])) {
            return;
        }
        $term = trim($data[$formName]);
        if ( ! $term) {
            return;
        }
        $qb->andWhere("e.{$fieldName} like :{$fieldName}");
        $qb->setParameter("{$fieldName}", "%{$term}%");
    }

    private function arrayPart(QueryBuilder $qb, array $data, string $fieldName, string $formName) : void {
        if ( ! isset($data[$formName])) {
            return;
        }
        /** @var Collection $list */
        $list = $data[$formName];
        if ( ! count($list)) {
            return;
        }
        $qb->andWhere("e.{$fieldName} IN (:{$fieldName})");
        $qb->setParameter($fieldName, is_array($list) ? $list : $list->toArray());
    }

    public function indexQuery() : Query {
        return $this->createQueryBuilder('clipping')
            ->addSelect('CAST(clipping.number as integer) HIDDEN n')
            ->orderBy('n', 'ASC')
            ->addOrderBy('clipping.date', 'ASC')
            ->addOrderBy('clipping.id', 'ASC')
            ->getQuery();
    }

    public function searchQuery($data) {
        $qb = $this->createQueryBuilder('e');
        $this->fulltextPart($qb, $data, 'transcription', 'transcription');

        if (isset($data['number']) && $data['number']) {
            $term = trim($data['number']);
            $qb->andWhere('e.number like :number');
            $qb->setParameter('number', "{$term}%");
        }

        $this->textPart($qb, $data, 'writtenDate', 'writtenDate');
        $this->textPart($qb, $data, 'date', 'date');

        $this->arrayPart($qb, $data, 'category', 'category');
        $this->arrayPart($qb, $data, 'source', 'source');

        if (isset($data['order']) && $data['order']) {
            switch ($data['order']) {
                case 'date':
                    $qb->orderBy('e.date', 'ASC');

                    break;

                case 'number':
                    $qb->orderBy('CAST(e.number AS integer)');
                    $qb->addOrderBy('e.number', 'ASC');

                    break;

                default:
            }
        }

        return $qb->getQuery();
    }

    public function categoryQuery(Category $category) : Query {
        $qb = $this->createQueryBuilder('e');
        $qb->addSelect('CAST(e.number as integer) HIDDEN n');
        $qb->andWhere('e.category = :category');
        $qb->setParameter('category', $category);
        $qb->orderBy('n', 'ASC');
        $qb->addOrderBy('e.date', 'ASC');
        $qb->addOrderBy('e.id', 'ASC');

        return $qb->getQuery();
    }

    public function sourceQuery(Source $source) : Query {
        $qb = $this->createQueryBuilder('e');
        $qb->addSelect('CAST(e.number as integer) HIDDEN n');
        $qb->andWhere('e.source = :source');
        $qb->setParameter('source', $source);
        $qb->orderBy('n', 'ASC');
        $qb->addOrderBy('e.date', 'ASC');
        $qb->addOrderBy('e.id', 'ASC');

        return $qb->getQuery();
    }
}

<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InstagramImageRepository extends EntityRepository {

  public function findByTag($tag, $orderBy = "DESC", $limit = 20) {

    $st = '%"' . $tag . '"%';
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->select('i')
            ->from('MainEntityBundle:InstagramImage', 'i')
            ->where($qb->expr()->like('i.tags', ':t'))
            ->setParameter('t', $st)
            ->orderBy('i.createdTime', $orderBy)
            ->setMaxResults($limit)
    ;
    $result = $qb->getQuery()->getResult();
    return $result;
  }

  public function findByTagDateRange($tag, $start, $end, $orderBy = "DESC") {

    $st = '%"' . $tag . '"%';
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->select('i')
            ->from('MainEntityBundle:InstagramImage', 'i')
            ->where('i.createdTime >= :start')
            ->andWhere('i.createdTime <= :end')
            ->andWhere($qb->expr()->like('i.tags', ':t'))
            ->setParameter('start', $start)
            ->setParameter('t', $st)
            ->setParameter('end', $end)
            ->orderBy('i.createdTime', $orderBy)
    ;
    $result = $qb->getQuery()->getResult();
    return $result;
  }

}

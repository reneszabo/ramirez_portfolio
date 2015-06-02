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

}

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

  public function findTop10UsersDateRange($tag, $start, $end, $orderBy = "DESC", $limit = 10) {
    $st = '%"' . $tag . '"%';
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->add('select', 'i.user, count(i.user) as cnt')
            ->from('MainEntityBundle:InstagramImage', 'i')
            ->where('i.createdTime >= :start')
            ->andWhere('i.createdTime <= :end')
            ->andWhere($qb->expr()->like('i.tags', ':t'))
            ->setParameter('start', $start)
            ->setParameter('t', $st)
            ->setParameter('end', $end)
            ->groupBy('i.user')
            ->orderBy('cnt', $orderBy)
            ->having('cnt>1')
            ->setMaxResults($limit)
    ;
    $result = $qb->getQuery()->getResult();
    return $result;
  }

  public function countImagesInDateRange($tag, $start, $end, $orderBy = "ASC") {
    $st = '%"' . $tag . '"%';
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->add('select', 'count(i.user)')
            ->from('MainEntityBundle:InstagramImage', 'i')
            ->where('i.createdTime >= :start')
            ->andWhere('i.createdTime <= :end')
            ->andWhere($qb->expr()->like('i.tags', ':t'))
            ->setParameter('start', $start)
            ->setParameter('t', $st)
            ->setParameter('end', $end)
            ->orderBy('i.createdTime', $orderBy)
    ;
    $result = $qb->getQuery()->getSingleScalarResult();
    return $result;
  }

  public function findImagesWithDateRange($tag, $start, $end, $page, $orderBy = "DESC", $limit = 20) {
    $start = ($page*1) * ($limit*1)*1;
    $st = '%"' . $tag . '"%';
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->add('select', 'i.createdTime, i.user, i.images')
            ->from('MainEntityBundle:InstagramImage', 'i')
            ->where('i.createdTime >= :start')
            ->andWhere('i.createdTime <= :end')
            ->andWhere($qb->expr()->like('i.tags', ':t'))
            ->setParameter('start', $start)
            ->setParameter('t', $st)
            ->setParameter('end', $end)
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->orderBy('i.createdTime', $orderBy)
    ;
    $result = $qb->getQuery()->getResult();
    return $result;
  }

}

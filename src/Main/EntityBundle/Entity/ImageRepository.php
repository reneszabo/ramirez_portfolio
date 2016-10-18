<?php

namespace Main\EntityBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository {

  public function findAllOrderByCreatedAt() {
    return $this->getEntityManager()
                    ->createQuery(
                            'SELECT i FROM MainEntityBundle:Image i ORDER BY i.createdAt ASC'
                    )
                    ->getResult();
  }

}

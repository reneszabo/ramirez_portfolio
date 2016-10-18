<?php

namespace Main\Page\BackendBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;
use Main\EntityBundle\Entity\Image;

class ImageToIdTransformer implements DataTransformerInterface {

  /**
   * @var EntityManager
   */
  private $em;

  /**
   * @param EntityManager $em
   */
  public function __construct(EntityManager $em) {
    $this->em = $em;
  }

  /**
   * Transforms an object (Supplier) to a string (number).
   *
   * @param  Image|null $image
   * @return string
   */
  public function transform($image) {
    if (null === $image) {
      return "";
    }

    return $image->getId();
  }

  /**
   * Transforms a string (number) to an object (issue).
   *
   * @param  string $number
   *
   * @return Image|null
   *
   * @throws TransformationFailedException if object (Image) is not found.
   */
  public function reverseTransform($number) {
    if (!$number) {
      return null;
    }

    $image = $this->em->getRepository('MainEntityBundle:Image')
            ->findOneBy(array('id' => $number))
    ;
    return $image;
  }

}

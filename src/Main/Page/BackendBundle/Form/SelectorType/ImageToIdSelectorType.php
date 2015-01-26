<?php

namespace Main\Page\BackendBundle\Form\SelectorType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Main\Page\BackendBundle\Form\DataTransformer\ImageToIdTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageToIdSelectorType extends AbstractType {

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

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $transformer = new ImageToIdTransformer($this->em);
    $builder->addModelTransformer($transformer);
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'invalid_message' => 'The selected issue does not exist',
    ));
  }

  public function getParent() {
    return 'text';
  }

  public function getName() {
    return 'image_to_id';
  }

}

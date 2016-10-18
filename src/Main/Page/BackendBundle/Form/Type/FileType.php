<?php

namespace Main\Page\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('path', 'text', array(
                'required' => true,
    ))
            ->add('orderLike', 'text', array(
                'required' => true,
    ))
          ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'data_class' => 'Main\EntityBundle\Entity\File'
    ));
  }

  public function getName() {
    return 'file';
  }

}

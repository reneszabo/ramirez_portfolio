<?php

namespace Main\Page\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('title', 'text', array(
                'required' => true,
                'max_length' => 255,
                'attr' => array()
            ))
            ->add('content', 'textarea', array(
                'required' => false,
                'max_length' => 255,
                'attr' => array('class' => 'summernote')
            ))
            ->add('save', 'submit', array('attr' => array('class' => 'pull-right btn btn-primary'), 'label' => 'save'))

    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'data_class' => 'Main\EntityBundle\Entity\Post'
    ));
  }

  public function getName() {
    return 'post';
  }

}

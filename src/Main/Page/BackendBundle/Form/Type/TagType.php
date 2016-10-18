<?php

namespace Main\Page\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TagType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('name', 'text', array(
                'required' => true,
                'max_length' => 32,
                'attr' => array()
            ))
            ->add('save', 'submit', array('attr' => array('class' => 'pull-right btn btn-primary'), 'label' => 'save'))
    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'data_class' => 'Main\EntityBundle\Entity\Tag'
    ));
  }

  public function getName() {
    return 'tag';
  }

}

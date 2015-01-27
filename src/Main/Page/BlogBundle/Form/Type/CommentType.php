<?php

namespace Main\Page\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('comment', 'textarea', array(
                'required' => false,
                'max_length' => 255,
                'attr' => array('class' => 'summernote')
            ))
            ->add('save', 'submit', array('attr' => array('class' => 'pull-right btn btn-primary'), 'label' => 'send comment'))

    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'data_class' => 'Main\EntityBundle\Entity\Comment'
    ));
  }

  public function getName() {
    return 'comment';
  }

}

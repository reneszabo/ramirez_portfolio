<?php

namespace InstagramBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstagramDateFormFilter extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('created_time_start', 'datetime', array(
                'input' => 'timestamp',
                'label' => "Start from (assume time 12:00 AM)",
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy 00:00:00',
//                'model_timezone' => 'GMT',
//                'view_timezone'     =>'GMT',
                'attr' => array('class' => 'date from')
            ))
            ->add('created_time_end', 'datetime', array(
                'input' => 'timestamp',
                'label' => "End in (assume time 11:59 PM)",
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy 23:59:59',
//                'model_timezone' => 'GMT',
//                'view_timezone'     =>'GMT',
                'attr' => array('class' => 'date to')
            ))
            ->add('save', 'submit', array('attr' => array('class' => 'pull-right btn btn-default'), 'label' => 'search'))
    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'data_class' => 'InstagramBundle\Form\Object\InstagramDateFilter'
    ));
  }

  public function getName() {
    return 'instagram_date_filter';
  }

}

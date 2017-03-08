<?php
// src/OC/PlatformBundle/Form/AdvertType.php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class AdvertItineraireEditType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->remove('categories')
    ;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'oc_platformbundle_advert_edit';  
  }
  
  public function getParent()
  {
      return new AdvertItineraireType();
  }
}
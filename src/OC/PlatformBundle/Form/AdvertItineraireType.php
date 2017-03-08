<?php
// src/OC/PlatformBundle/Form/AdvertType.php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertItineraireType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('price',     'money')
      ->add('title',     'text')
      ->add('author',    'text')
      ->add('user',      new UserType())
      ->add('content',   'textarea')
      ->add('image',      new ImageType())
      ->add('categories', 'entity', array(
        'class'    => 'OCPlatformBundle:Category',
        'property' => 'name',
        'multiple' => true,
        'expanded' => false
      ))
      ->add('itineraire',  new ItineraireType())
      ->add('save',      'submit')
    ;

    // On ajoute une fonction qui va ecouter l'evenement PRE_SET_DATA
    $builder->addEventListener(
      FormEvents::PRE_SET_DATA,
      function(FormEvent $event) {
        // Recuperer notre objet Advert sous-jacent
        $advert = $event->getData();

        if (null === $advert) {
          return;
        }

        if (!$advert->getPublished() || null === $advert->getId()) {
          $event->getForm()->add('published', 'checkbox', array('required' => false));
        } else {
          $event->getForm()->remove('published');
        }
      }
    );
  }

  /**
   * @param OptionsResolverInterface $resolver
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'OC\PlatformBundle\Entity\Advert'
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'oc_platformbundle_advert';  
  }
}
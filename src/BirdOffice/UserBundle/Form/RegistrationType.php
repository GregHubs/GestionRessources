<?php

namespace Imdb\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // On étend ici le formulaire d'inscription de FOSUserBundle
        $builder
            ->add('username', 'text', array('label' => 'Nom d\'utilisateur'))
            ->add('managedBy', 'entity', array('label' => 'Manager', 'class' => 'UserBundle:User', 'property' => 'manager'))
            ->getForm();
    }

    // La méthode getParent permet de récupérer le formulaire initial de FOSUserBundle
    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'bird_user_registration';
    }
}
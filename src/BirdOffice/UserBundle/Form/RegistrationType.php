<?php

namespace BirdOffice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', 'choice',
                array(
                    'label'         => 'Civilité',
                    'choices'   => array(
                        '1'=>'M',
                        '2'=>'Mme'
                    )
                )
            )
            ->add('name', 'text',
                array(
                    'label' => 'Nom'
                )
            )
            ->add('username', 'text',
                array(
                    'label' => 'Prénom'
                )
            )
            ->add('email', 'text',
                array(
                    'label' => 'Email'
                )
            )
            ->add('plainPassword', 'repeated',
                array(
                    'type' => 'password',
                    'first_options' => array('label' => 'Mot de passe'),
                    'second_options' => array('label' => 'Confirmation mot de passe'),
                    'invalid_message' => 'fos_user.password.mismatch'
                )
            )
            ->add('managedBy', EntityType::class,
                array(
                    'label' => 'Manager',
                    'class' => 'UserBundle:User'
                )
            )
        ;
    }

    // La méthode getParent permet de récupérer le formulaire initial de FOSUserBundle
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'bird_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }

}
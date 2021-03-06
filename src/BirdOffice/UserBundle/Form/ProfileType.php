<?php

namespace BirdOffice\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class ProfileType extends AbstractType
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
            ->add('lastname', 'text',
                array(
                    'label' => 'Nom'
                )
            )
            ->add('firstname', 'text',
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
            ->add('manager', EntityType::class,
                array(
                    'label' => 'Manager',
                    'class' => 'UserBundle:User',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->select('u')
                            ->where('u.roles LIKE :roles')
                            ->setParameter('roles', "%ROLE_SUPER_ADMIN%")
                            ;
                    },
                )
            )
        ;
    }

    // La méthode getParent permet de récupérer le formulaire initial de FOSUserBundle
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'bird_user_profile';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }

}
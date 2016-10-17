<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                TextType::class
            )
            ->add(
                'password',
                PasswordType::class
            )
            ->add(
                'email',
                EmailType::class
            )
            ->add(
                'firstName',
                TextType::class
            )
            ->add(
                'lastName',
                TextType::class
            )
            ->add(
                'address',
                TextType::class,
                [
                    'required' => false
                ]
            )
            ->add(
                'phone',
                TextType::class,
                [
                    'required' => false
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'required' => false
                ]
            )
            ->add(
                'register',
                SubmitType::class,
                [
                    'label' => 'Registruotis'
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\User'
            ]
        );
    }
}

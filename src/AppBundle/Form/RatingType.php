<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'comment',
                TextareaType::class
            )
            ->add(
                'rate',
                ChoiceType::class,
                [
                    'choices' => [
                        '5'=>'5',
                        '4'=>'4',
                        '3'=>'3',
                        '2'=>'2',
                        '1'=>'1'
                    ],
                    'multiple' => false
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
                'data_class' => 'AppBundle\Entity\Rating'
            ]
        );
    }
}

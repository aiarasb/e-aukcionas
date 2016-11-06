<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class
            )
            ->add(
                'description',
                TextareaType::class
            )
            ->add(
                'basePrice',
                NumberType::class,
                [
                    'required' => false
                ]
            )
            ->add(
                'buyNowPrice',
                NumberType::class,
                [
                    'required' => false
                ]
            )
            ->add(
                'auctionStart',
                DateTimeType::class,
                [
                    'html5' => true,
                    'widget' => 'single_text',
                    'required' => false
                ]
            )
            ->add(
                'auctionEnd',
                DateTimeType::class,
                [
                    'html5' => true,
                    'widget' => 'single_text',
                    'required' => false
                ]
            )
            ->add(
                'create',
                SubmitType::class,
                [
                    'label' => 'Pridėti'
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
                'data_class' => 'AppBundle\Entity\Item'
            ]
        );
    }
}

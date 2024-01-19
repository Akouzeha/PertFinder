<?php

namespace App\Form;

use App\Entity\Diagram;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ChartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
            [
                'attr' => [
                    'class' => 'chart-form-input',
                    'placeholder' => 'Titre du diagramme'
                ]
            ])
            ->add('description', TextareaType::class,
            [
                'attr' => [
                    'class' => 'chart-form-textArea',
                    'placeholder' => 'Description du diagramme'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Diagram::class,
        ]);
    }
}

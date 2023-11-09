<?php

namespace App\Form;

use App\Entity\Project;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
            [
                'label' => 'Titre du projet',
                'attr' => [
                    'class' => 'project-form-input'
                ]
            ])
            ->add('dateDebut', DateType::class,
            [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'project-form-input'
                ]
            ])
            
            ->add('dateFin', DateType::class,
            [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'project-form-input'
                ]
            ])
            ->add('description', TextareaType::class,
            [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-textArea'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}

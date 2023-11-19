<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la tâche',
                'attr' => [
                    'class' => 'task-form-input'
                ]
            ])
            ->add('optTime', DateType::class, [
                'label' => 'Optemistic Time',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'task-form-input'
                ]
            ])
            ->add('pesTime', DateType::class, [
                'label' => 'Pessimistic Time',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'task-form-input'
                ]
            ])
            ->add('mosTime', DateType::class, [
                'label' => 'Most Likely Time',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'task-form-input'
                ]
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'task-form-textArea'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}

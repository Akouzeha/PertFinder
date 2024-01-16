<?php

namespace App\Form;

use App\Entity\Task;

use Symfony\Component\Form\AbstractType;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la tâche',
                'attr' => [
                    'class' => 'task-form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom de tâche',
                    ]),
                ],
            ])
            ->add('optTime', IntegerType::class, [
                'label' => 'Temps optimiste',
                'attr' => [
                    'class' => 'task-form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un temps optimiste',
                    ]),
                    new Range([
                        'min' => 0,
                        'minMessage' => 'Le temps optimiste doit être supérieur à 0',
                    ]),
                ],
            ])
            ->add('pesTime', IntegerType::class, [
                'label' => 'Temps pessimiste',
                'attr' => [
                    'class' => 'task-form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un temps pessimiste',
                    ]),
                    new Range([
                        'min' => 0,
                        'minMessage' => 'Le temps pessimiste doit être supérieur à 0',
                    ]),
                ],
            ])
            ->add('mosTime', IntegerType::class, [
                'label' => 'Temps le plus probable',
                'attr' => [
                    'class' => 'task-form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un temps le plus probable',
                    ]),
                    new Range([
                        'min' => 0,
                        'minMessage' => 'Le temps le plus probable doit être supérieur à 0',
                    ]),
                ],
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

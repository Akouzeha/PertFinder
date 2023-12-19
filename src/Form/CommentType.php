<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('contenu', TextareaType::class,
        [
            'attr' => [
                'class' => 'form-textArea',
                'placeholder' => 'Votre commentaire'
            ]
        ])
        ->add('poster', SubmitType::class,
        [
            'label' => 'Poster',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ])
        ;

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}

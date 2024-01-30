<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Message;
use PharIo\Manifest\Email;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Component\Validator\Constraints\Date;

class ResponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
           
            ->add('contenu', TextareaType::class, [
                
                'label' => false,
                'attr' => ['class' => 'form-control',
                'placeholder' => 'écrivez votre message'],
            ])
            ->add('answer', SubmitType::class, [

                'attr' => ['class' => 'send-button'],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}

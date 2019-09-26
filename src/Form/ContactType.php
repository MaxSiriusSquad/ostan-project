<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => false,
                'constraints' => new NotBlank,
                // pour surcharger la mise en forme du formulaire
                'attr' => [
                    'class' => 'form-control-title',
                    'placeholder' => 'Titre du message'
                ],
            ])
            ->add('email', null, [
                'label' => false,
                'constraints' => new NotBlank,
                'attr' => [
                    'class' => 'form-control-email',
                    'placeholder' => 'Votre email'
                ],
            ])
            ->add('content', null, [
                'label' => false,
                'constraints' => new NotBlank,
                'attr' => [
                    'class' => 'form-control-content',
                    'placeholder' => 'Votre message'
                ],
            ])
            
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}

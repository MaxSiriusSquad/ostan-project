<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', FileType::class, [
                'data_class' => null,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Photo du profil'
                ], 
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'empty_data' => '',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez renseigner un prénom',
                    ]),
                ],   
            ])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('username', TextType::class, ['label' => 'Pseudo'])
            ->add('companyname', TextType::class, ['label' => 'Nom de l\'entreprise'])
            ->add('description', TextType::class, ['label' => 'description'])
            ->add('birthdate', BirthdayType::class, [
                'widget' => 'choice',
            ])
            ->add('phonenumber')
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes ne correspondent pas.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => '(Laissez vide si inchangé)'
                    ]
                ],
                'second_options' => [
                    'label' => 'Vérifiez le mot de passe',
                    'attr' => [
                        'placeholder' => '(Laissez vide si inchangé)'
                    ]
                ],
            ])
            ->add('jobs', EntityType::class, [
                'class' => Job::class,
                'multiple' => true,
                'expanded' => true,
            ])
            
            ;


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            //cet attribut permet de desactiver la validation HTML5
            //malgres le fait qu'il soit pratique, il est souvent demandé quelle soient desactivée pour que  l'intégration des messages d'erreurs prenne le pas sur ce que le navigateur propose
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}

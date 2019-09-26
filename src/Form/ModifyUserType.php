<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\TagRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ModifyUserType extends AbstractType
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
                'required'=>false
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'empty_data' => '',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez renseigner un prénom',
                    ]),
                ]])
            ->add('lastname')
            ->add('companyname')
            ->add('description')
            ->add('siret')
            ->add(
                'password',
                RepeatedType::class,
                [
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
                    ]
                ]
            )
            ->add('birthdate', BirthdayType::class, [
                'widget' => 'choice'])
                
            ->add('email')
            ->add('address')
            ->add('phonenumber')
            ->add('jobs')

            ->add('tags', null, [
                'attr' => [
                    'class'=>'select-tags'
                ],
                'label_attr'=>[
                    'class'=>'label_select-tags'
                ],
                'query_builder' => function (TagRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate']

        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\JobType;

use App\Repository\TagRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', FileType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Avatar *'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Votre avatar ne doit pas être vide',
                    ]),
                ],                
            ])

            ->add('firstname', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom *'
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom *'
                ],
            ])
            ->add('username', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Pseudo *'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Description *',
                    'cols' => '50', 
                    'rows' => '5'
                ],
            ])

            ->add('birthdate', BirthdayType::class, [
                'years' => range(1940,2019),
                'widget' => 'choice',
                'label' => 'Date de naissance: *',
            ])
            ->add('phonenumber', TelType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Numéro de téléphone'
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email *'
                ],
            ])

            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options'  => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Mot de passe *'
                    ]
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Vérifier le mot de passe *'
                    ]
                ],
                'attr' => [
                    'placeholder' => 'Mot de passe'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins 6 caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('jobs', EntityType::class, [
                'class' => Job::class,
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'custom-control custom-checkbox'],
                'label_attr' => array('class' => 'pure-material-checkbox'),

            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'multiple' => true,
                'attr' => ['class' => 'custom-control custom-checkbox'],
                'label_attr' => array('class' => 'pure-material-checkbox'),
                'query_builder' => function (TagRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('siret', NumberType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Siret *'
                ],])
                ->add('companyname', TextType::class,[
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Nom de l\'entreprise *'
                    ],])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Accepter les conditions générales d\'utilisation',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les_conditions générales d\'utilisation pour continuer.',
                    ]),
                ],
            ]);    
            
            

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default'],
        ]);
    }
}

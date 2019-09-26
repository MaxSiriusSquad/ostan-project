<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('jobs', EntityType::class, [
                'class' => Job::class,
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'custom-control custom-checkbox'],
                'label_attr' => array('class' => 'pure-material-checkbox'),
                
            ])
            // ->add('users', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'username',
            //     'multiple' => true,
            // ])
            ->add('Rechercher', SubmitType::class);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => Post::class,
        ]);
    }
}
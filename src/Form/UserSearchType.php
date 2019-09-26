<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\TagRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserSearchType extends AbstractType
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
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'query_builder' => function (TagRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('Rechercher', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => User::class,
        ]);
    }
}

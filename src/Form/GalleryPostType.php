<?php

namespace App\Form;

use App\Entity\GalleryPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class GalleryPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Titre de la galerie'
            ],])
        ->add('picture1', FileType::class, [
            'data_class' => null,
            'label' => 'Affiche (jpg,png,gif)',
            'required' => false,
            'attr' => [
                'placeholder' => 'Image'
            ],])
        ->add('picture2', FileType::class, [
            'data_class' => null,
            'label' => 'Affiche (jpg,png,gif)',
            'required' => false,
            'attr' => [
                'placeholder' => 'Image'
            ],])
        ->add('picture3', FileType::class, [
            'data_class' => null,
            'label' => 'Affiche (jpg,png,gif)',
            'required' => false,
            'attr' => [
                'placeholder' => 'Image'
            ],])
        ->add('description', TextType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Message'
            ],])
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GalleryPost::class,
        ]);
    }
}

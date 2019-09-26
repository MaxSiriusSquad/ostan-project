<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Message;
use App\Entity\Post;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class MessageType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // récupérer l'url
        $url = ($_SERVER["REQUEST_URI"]);

        

        // construit un formulaire différent selon l'url, à partir de la meme Entité
        if($url == '/message/new'){
        
            $builder

                ->add('title', TextType::class, array(
                    'label' => false,
                    'attr' =>array(
                        'placeholder' => 'Titre du message'
                    )
                    ))
                    
                ->add('content', TextareaType::class ,array(
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Message...'
                    )
                ))
                //->add('createdAt')
                //->add('email')
                
               

                // pour définir, avec menu déroulant, le destinataire du message
                
                ->add('userReceiver', EntityType::class,[
                    'label' => false,
                    'class'=> User::class,
                    'expanded' =>false,
                    'multiple' =>false,
                    'query_builder' => function (UserRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.username', 'ASC');
                    },
                    
                ])
                


                
            ;


        // TEST CORRECTION PB D'ENVOI DE MAIL A SOI MEME //
        // pour intercepter la soumission du formulaire avant sa soumission et en modifier les champs
        /*
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
                $user = $event->getData()->getUser();

                if (null !== $event->getData()->getUserReceiver()) {
                    // we don't need to add the userReceiver field because
                    // the message will be addresse $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            
                // grab the user, do a quick sanity check that one exists
                $user = $this->security->getUser();
                if (!$user) {
                    throw new \LogicException(
                        'Ce formulaire ne peut être utilisé sans être authentifié. Merci de vous connecter au préalable.'
                    );
                }
                    return;
                }
    
                $form = $event->getForm();
    
                // voir option Lucie
                $formOptions = [
                    'class' => User::class,
                    'expanded' =>false,
                    'multiple' =>false,
                    'query_builder' => function (UserRepository $userRepository) use ($user) {
                        return $userRepository->findAllExceptUser($user);
                    },
                ];
    
                // create the field, this is similar the $builder->add()
                // field name, field type, field options
                $form->add('userReceiver', EntityType::class, $formOptions);
            });

        */
        // FIN TEST CORRECTION BUG //

         }else {
             $builder

            
                ->add('content', TextareaType::class ,array(
                    'label' => false,
                    'attr' => array(
                        'placeholder' => 'Votre message'
                        
                    )
                    ));
                
                 
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}

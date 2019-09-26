<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Post;
use App\Entity\User;
use App\Form\JobType;
use App\Form\UserType;
use App\Utils\Slugger;
use App\Entity\GalleryPost;
use App\Form\ModifyUserType;
use App\Form\UserSearchType;
use App\Form\GalleryPostType;
use App\Repository\JobRepository;
use App\Repository\UserRepository;
use App\Repository\GalleryPostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     *
     * Page de profil
     *
     * @Route("/profil/{slug}", name="user_show", methods ={"GET","POST"})
     */
    public function show(GalleryPostRepository $galleryPost, UserRepository $userRepository, JobRepository $jobRepository, User $user, Request $request, Slugger $slugger)
    {
        $gallery = new GalleryPost();
        $formGallery = $this->createForm(GalleryPostType::class, $gallery);
        $formGallery->handleRequest($request);

        if ($formGallery->isSubmitted() && $formGallery->isValid()) {

            $file = $gallery->getPicture1();

            if(!is_null($gallery->getPicture1())){
                //je genere un nom de fichier unique pour eviter d'ecraser un fichier du meme nom & je concatene avec l'extension du fichier d'origine
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                try {
                    //je deplace mon fichier dans le dossier souhaité
                    $file->move(
                        $this->getParameter('image_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    dump($e);
                }
                $gallery->setPicture1($fileName);
            }

            $file = $gallery->getPicture2();
            if(!is_null($gallery->getPicture2())){
                //je genere un nom de fichier unique pour eviter d'ecraser un fichier du meme nom & je concatene avec l'extension du fichier d'origine
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                try {
                    //je deplace mon fichier dans le dossier souhaité
                    $file->move(
                        $this->getParameter('image_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    dump($e);
                }

                $gallery->setPicture2($fileName);
            }

            $file = $gallery->getPicture3();

            if(!is_null($gallery->getPicture3())){
                //je genere un nom de fichier unique pour eviter d'ecraser un fichier du meme nom & je concatene avec l'extension du fichier d'origine
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                try {
                    //je deplace mon fichier dans le dossier souhaité
                    $file->move(
                        $this->getParameter('image_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    dump($e);
                }

                $gallery->setPicture3($fileName);
            }

            $gallery->setUser($user);

            $slug = $slugger->slugify($user->getUsername());
            $user->setSlug($slug);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($gallery);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre document a bien été enregistré !'
            );

            return $this->redirectToRoute('user_show', ['slug' => $user->getSlug()]);
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'galleryPost' => $galleryPost,
            'formGallery' => $formGallery->createView(),
        ]);
    }

    /**
     * Modification d'un user :
     *
     * @Route("/profil/edit/{slug}", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder, UserRepository $userRepository, Slugger $slugger): Response
    {

        
        // $job = new Job();

        // $jobName = $job->getName();
        // $job->setName($jobName);

        // $formJob = $this->createForm(JobType::class, $job);
        // $formJob->handleRequest($request);


        // if ($formJob->isSubmitted() && $formJob->isValid()) {
        //     $formName = $user->getName();
        //     $criterias = $request->request->get($formName); 
        //     $users = $userRepository->findJob($criterias);

        //     $slug = $slugger->slugify($user->getUsername());
        //     $user->setSlug($slug);
            
        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager -> persist($job);
        //     $entityManager -> flush();
            
           
        //     return $this->redirectToRoute('user_show', ['slug' => $user->getSlug()]);
        // }

        //Je récupère l'ancien avatar
        $oldAvatar = $user->getAvatar();
        // Je récupère l'ancien mot de passe :
        $oldPassword = $user->getPassword();

        $form = $this->createForm(ModifyUserType::class, $user);

        // Met à jour l'objet User avec les nouvelles valeurs
        // Si l'objet User n'a pas eu de nouveau mot de passe alors le champ "mot de passe" est vide et conserve donc l'ancien mot de passe
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(!is_null($user->getAvatar())){
                $file = $user->getAvatar();
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('image_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    dump($e);
                }
                
                $user->setAvatar($fileName);

                if(!empty($oldAvatar)){
                    unlink(
                        $this->getParameter('image_directory') .'/'.$oldAvatar
                    );
                }

            } else {
                $user->setAvatar($oldAvatar);//ancien nom de fichier
            }

            /*
                Si je ne souhaite pas modifier le mot de passe en édition, alors je peux avoir le comportement suivant : mot de passe laissé vide si inchangé.
                Ainsi je dois tester si le nouveau mot de passe est vide, et si c'est le cas je récupère l'ancien mot de passe
            */

            // Si le mot de passe est nul
            if (is_null($user->getPassword())) {
                // Le mot de passe encodé est l'ancien mot de passe
                $encodedPassword = $oldPassword;
            } else {
                // Comme dans la fonction new
                $encodedPassword = $encoder->encodePassword($user, $user->getPassword());
            }

            // Comme dans la fonction new
            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'info',
                'Modification effectuée !'
            );
            return $this->redirectToRoute('user_show', [
                'slug' => $user->getSlug()
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            // 'job' => $job,
            'form' => $form->createView(),
            // 'formJob' => $formJob->createView(),
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    /**   
     * Suppression d'un user :
     *
     * @Route("/profil/delete/{slug}", name="user_delete", methods={"DELETE","POST"})
     */
    public function delete(Request $request, User $user, Slugger $slugger): Response
    {

        // Je dois d'abord effacer la session pour supprimer le user avec lequel je suis connecté :
        $session = $this->get('session');
        $session = new Session();
        $session->invalidate();

        if ($this->isCsrfTokenValid('delete'.$user->getSlug(), $request->request->get('_token'))) {

            $slug = $slugger->slugify($user->getUsername());
            $user->setSlug($slug);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash(
                'danger',
                'Suppression effectuée !'
            );
        }
        return $this->redirectToRoute('advice_post');
    }

    /**
     * LISTE DES USERS + RESULTATS DE LA RECHERCHE 
     * @Route("/users", name="user_list")
     */
     public function userList(Request $request, UserRepository $userRepository){
        $formSearchUser = $this->createForm(UserSearchType::class);

        $formSearchUser->handleRequest($request);
        if ($formSearchUser->isSubmitted() && $formSearchUser->isValid()) {
            $formName = $formSearchUser->getName();
            $criterias = $request->request->get($formName);

            $users = $userRepository->searchHome($criterias);

        } else {
            // Classés du plus récent au moins récent
            $users = $userRepository->findBy(array(), array('createdAt' => 'DESC'));
            // $users = $userRepository->findAll();
        }

        return $this->render('user/userList.html.twig', [
            'users' => $users,
            // 'formSearchUser' => $formSearchUser->createView(),
        ]);

     }
     
     public function userSearchForm(Request $request, UserRepository $userRepository){
        $formSearchUser = $this->createForm(UserSearchType::class);

        // $users = [];

        $formSearchUser->handleRequest($request);
        // if ($formSearchUser->isSubmitted() && $formSearchUser->isValid()) {
        //     $formName = $formSearchUser->getName();
        //     $criterias = $request->request->get($formName);

        //     $users = $userRepository->searchHome($criterias);
        // }

        return $this->render('user/userListForm.html.twig', [
            // 'users' => $users,
            'formSearchUser' => $formSearchUser->createView(),
        ]);
     }

     public function usersNavList(UserRepository $userRepository){

        // Classés du plus récent au moins récent
        $users = $userRepository->findBy(array(), array('createdAt' => 'DESC'));
        return $this->render('user/usersNavList.html.twig', [
           'users' => $users,
       ]);
    }
}

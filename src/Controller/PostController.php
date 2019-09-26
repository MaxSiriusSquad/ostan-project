<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Post;
use App\Form\JobType;
use App\Form\PostType;
use App\Utils\Slugger;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\UserSearchType;
use App\Repository\JobRepository;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * Page d'accueil visiteur avec la liste des ARTICLES DE CONSEILS :
     * 
     * @Route("/", name="advice_post", methods={"GET"})
     */
    public function advicePostList(PostRepository $postRepository, Request $request, PaginatorInterface $paginator)
    {

        $advicePosts = $this->getDoctrine()->getRepository(Post::class)->findAllAdvicePost();
        $advicelistPosts = $paginator->paginate(
            $advicePosts, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('post/advice_post/index.html.twig', [
            'advicePosts' => $advicePosts,
            'advicelistPosts' => $advicelistPosts
        ]);
    }

    /**
     * Page d'accueil utilisateur (après inscription/connexion) avec la liste des ANNONCES :
     * 
     * @Route("/annonces", name="ad_post")
     */
    public function adList(JobRepository $jobRepository, Request $request, PostRepository $postRepository, TagRepository $tagRepository, UserRepository $userRepository, PaginatorInterface $paginator)
    {
       
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAllAdPost();
        $listPosts = $paginator->paginate(
            $posts, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
    
        $tags = $tagRepository->findAll();
        

        return $this->render('post/ad_post/index.html.twig', [
            'posts' => $posts,
            'tags' => $tags,
            'listPosts' => $listPosts
         
        ]);
    }

    /**
     * @Route("/searchUser", name="searchUser")
     */
    public function searchUser(Request $request)
    {
        dump($request->request);
        die;
    }

    /**
     * Page de détail d'un ARTICLE DE CONSEILS (pas de possibilité de commenter) :
     *
     * @Route("/advicepost/{slug}", name="advice_post_show", methods={"GET","POST"})
     */
    public function advicePostShow(Post $advicePost, Request $request, UserRepository $userRepository, CommentRepository $commentRepository, Slugger $slugger)
    {

        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {

            $comment->setPost($advicePost);
            $user = $userRepository->findOneByUsername('emoen');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('advice_post_show', ['slug' => $advicePost->getSlug()]);
        }
        return $this->render('post/advice_post/show.html.twig', [
            'advicePost' => $advicePost,
            'formComment' => $formComment->createView(),
        ]);
    }

    /**
     * Page de de détail d'une ANNONCE avec formulaire pour poster un commentaire (pour les utilisateurs exclusivement) :
     * 
     * @Route("/annonce/{slug}", name="ad_post_show", methods={"GET","POST"})
     */
    public function adPostShow(Post $post, Request $request, StatusRepository $statusRepository, Slugger $slugger)
    { 

        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);
        $user = $this->getUser();
        $defaultStatus = 'UNBLOCKED';
        $unblockedStatus = $statusRepository->findOneByCode($defaultStatus);


        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment->setPost($post);
            $comment->setUser($user);
            $comment->setStatus($unblockedStatus);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été enregistré !'
            );

            return $this->redirectToRoute('ad_post_show', ['slug' => $post->getSlug()]);
        }
        return $this->render('post/ad_post/show.html.twig', [
            'post' => $post,
            'formComment' => $formComment->createView(),
        ]);
    }

    // ANNONCES *****************************************************************************************

    /**
     * Page formulaire d'ajout d'une ANNONCE (pour les utilisateurs exclusivement)
     *
     * @Route("/annonces/new", name="ad_post_new", methods={"GET","POST"})
     */
    public function adNew(Request $request, StatusRepository $statusRepository, Slugger $slugger)
    {
        $statusCode = 'UNBLOCKED';
        $statusCode = $statusRepository->findOneByCode($statusCode);
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $post->getPicture1();

            if(!is_null($post->getPicture1())){
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
                $post->setPicture1($fileName);
            }

            $file = $post->getPicture2();
            if(!is_null($post->getPicture2())){
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

                $post->setPicture2($fileName);
            }

            $file = $post->getPicture3();

            if(!is_null($post->getPicture3())){
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

                $post->setPicture3($fileName);
            }

            $post->setStatus($statusCode);
            $post->setType('Annonce');
            $post->setUser($user);


            $slug = $slugger->slugify($post->getTitle());
            $post->setSlug($slug);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre annonce a bien été enregistrée !'
            );

            return $this->redirectToRoute('ad_post');
        }
        return $this->render('post/ad_post/new.html.twig', [
            'form' => $form->createView(),
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

    public function adPostsNavList(PostRepository $postRepository){

        // Classés du plus récent au moins récent
        $adPosts = $postRepository->findBy(array(), array('createdAt' => 'DESC'));
        return $this->render('post/ad_post/adPostsNavList.html.twig', [
           'adPosts' => $adPosts,
       ]);
    }

    /**
     * Suppression d'une annonce (pour qu'un user qui a posté une annonce puisse la supprimer)
     *
     * @Route("/annonces/delete/{slug}", name="adDelete", methods="DELETE")
     */
    public function adDelete(Request $request, Post $post)
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }
        return $this->redirectToRoute('ad_post');
    }

}

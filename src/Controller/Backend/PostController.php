<?php

namespace App\Controller\Backend;


use App\Entity\Post;
use App\Form\PostType;
use App\Utils\Slugger;
use App\Form\PostSearchType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


/**
 * 
 * @Route("/backend", name="backend_")
 */
class PostController extends AbstractController
{

    /**
     * LISTE DES ANNONCES + FILTRE/RECHERCHE PAR UTILISATEUR/JOB
     * @Route("/", name="adList")
     */
    public function adList(PostRepository $postRepository, UserRepository $userRepository, Request $request, PaginatorInterface $paginator)
    {
        $formSearchPost = $this->createForm(PostSearchType::class);

        $formSearchPost->handleRequest($request);
        if ($formSearchPost->isSubmitted() && $formSearchPost->isValid()) {
            //dd($request);
            // $formName= $formSearchPost->getName();
            // $criterias = $request->request->get($formName);
            $jobs = $formSearchPost->getData()['jobs'];

            $postsearch = $postRepository->searchAdList($jobs);
            //dd($criterias['users']);
        }else {
            
            // Classés par date de création, du plus récent au plus ancien
            $postsearch = $postRepository->findAllAdPost();            
        }        

        // PAGINATION //
        
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAllAdPost();

        $adListPost = $paginator->paginate(
            $posts, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            5 // Nombre de résultats par page
        );
        // FIN PAGINATION //
        

        return $this->render('backend/post/adList.html.twig', [
            'postsearch' => $postsearch,
            'adListPost' => $adListPost,
     
        ]);
    }

    public function postSearchForm(Request $request, PostRepository $postRepository){

        $formSearchPost = $this->createForm(PostSearchType::class);
        $formSearchPost->handleRequest($request);
    
        return $this->render('backend/post/adListForm.html.twig', [
            'formSearchPost' => $formSearchPost->createView(),
        ]);
     }



    public function postNavList(PostRepository $postRepository){

        // Classés du plus récent au moins récent
        $postsearch = $postRepository->findBy(array(), array('createdAt' => 'DESC'));
        return $this->render('backend/post/adListSearch.html.twig', [
            'postsearch' => $postsearch,
       ]);
    }

   /* FIN RECHERCHE */


    /**
     * @Route("/articles", name="advicePostList")
     */
    public function advicePostList(PostRepository $postRepository, Request $request, PaginatorInterface $paginator)
    {

        $posts = $this->getDoctrine()->getRepository(Post::class)->findAllAdvicePost();
        $listPost = $paginator->paginate(
            $posts, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            5 // Nombre de résultats par page
        );
        return $this->render('backend/post/advicePostList.html.twig', [
            'listPost' => $listPost

        ]);
    }

    // Création d'un article de conseil sur page d'accueil
    /**
     * @Route("/advice-post/new/", name="advicePostNew")
     */
    public function advicePostNew(Request $request, StatusRepository $statusRepository, Slugger $slugger)
    {
        $statusCode = 'UNBLOCKED';
        $statusCode= $statusRepository->findOneByCode($statusCode);
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        

        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $file1 = $post->getPicture1();

            if(!is_null($post->getPicture1())){
                //je genere un nom de fichier unique pour eviter d'ecraser un fichier du meme nom & je concatene avec l'extension du fichier d'origine
                $fileName1 = $this->generateUniqueFileName() . '.' . $file1->guessExtension();
                try {
                    //je deplace mon fichier dans le dossier souhaité
                    $file1->move(
                        $this->getParameter('image_directory'),
                        $fileName1
                    );
                } catch (FileException $e) {
                    dump($e);
                }
                $post->setPicture1($fileName1);
            }

            $file2 = $post->getPicture2();

            if(!is_null($post->getPicture2())){
                //je genere un nom de fichier unique pour eviter d'ecraser un fichier du meme nom & je concatene avec l'extension du fichier d'origine
                $fileName2 = $this->generateUniqueFileName().'.'.$file2->guessExtension();

                try {
                    //je deplace mon fichier dans le dossier souhaité
                    $file2->move(
                        $this->getParameter('image_directory'),
                        $fileName2
                    );
                } catch (FileException $e) {
                    dump($e);
                }

                $post->setPicture2($fileName2);
            }

            $file3 = $post->getPicture3();

            if(!is_null($post->getPicture3())){
                //je genere un nom de fichier unique pour eviter d'ecraser un fichier du meme nom & je concatene avec l'extension du fichier d'origine
                $fileName3 = $this->generateUniqueFileName().'.'.$file3->guessExtension();

                try {
                    //je deplace mon fichier dans le dossier souhaité
                    $file3->move(
                        $this->getParameter('image_directory'),
                        $fileName3
                    );
                } catch (FileException $e) {
                    dump($e);
                }

                $post->setPicture3($fileName3);
            }

            $post->setType('Article');
            $post->setStatus($statusCode);

            $slug = $slugger->slugify($post->getTitle());
            $post->setSlug($slug);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager -> persist($post);
            $entityManager -> flush();


            $this->addFlash(
                'success',
                'Votre article a bien été enregistré !'
            );

            return $this->redirectToRoute('backend_advicePostList');
        }
        return $this->render('backend/post/advicePostNew.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/post/edit/{slug}", name="advicePostEdit", methods="GET|POST")
     */
    public function advicePostEdit(Request $request, Post $post)
    {
        
        $oldPicture1 = $post->getPicture1();
        $oldPicture2 = $post->getPicture2();
        $oldPicture3 = $post->getPicture3();

        

        if(!empty($oldPicture1)) {
            $post->setPicture1(
                new File($this->getParameter('image_directory').'/'.$oldPicture1)
            );
        }
        
        if(!empty($oldPicture2)) {
            $post->setPicture2(
                new File($this->getParameter('image_directory').'/'.$oldPicture2)
            );
        }
        
        if(!empty($oldPicture3)) {
            $post->setPicture3(
                new File($this->getParameter('image_directory').'/'.$oldPicture3)
            );
        }


        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            if(!is_null($post->getPicture1())){

                $file1 = $post->getPicture1();
            
                $fileName1 = $this->generateUniqueFileName().'.'.$file1->guessExtension();

                try {
                    $file1->move(
                        $this->getParameter('image_directory'),
                        $fileName1
                    );
                } catch (FileException $e) {
                    dump($e);
                }
                
                $post->setPicture1($fileName1);

                if(!empty($oldPicture1)){

                    unlink(
                        $this->getParameter('image_directory') .'/'.$oldPicture1
                    );
                }

            } else {
                
                $post->setPicture1($oldPicture1);//ancien nom de fichier
            }

            if(!is_null($post->getPicture2())){

                $file2 = $post->getPicture2();
            
                $fileName2 = $this->generateUniqueFileName().'.'.$file2->guessExtension();

                try {
                    $file2->move(
                        $this->getParameter('image_directory'),
                        $fileName2
                    );
                } catch (FileException $e) {
                    dump($e);
                }
                
                $post->setPicture2($fileName2);

                if(!empty($oldPicture2)){

                    unlink(
                        $this->getParameter('image_directory') .'/'.$oldPicture2
                    );
                }

            } else {
                
                $post->setPicture2($oldPicture2);//ancien nom de fichier
            }

            if(!is_null($post->getPicture3())){

                $file3 = $post->getPicture3();
            
                $fileName3 = $this->generateUniqueFileName().'.'.$file3->guessExtension();

                try {
                    $file3->move(
                        $this->getParameter('image_directory'),
                        $fileName3
                    );
                } catch (FileException $e) {
                    dump($e);
                }
                
                $post->setPicture3($fileName3);

                if(!empty($oldPicture3)){

                    unlink(
                        $this->getParameter('image_directory') .'/'.$oldPicture3
                    );
                }

            } else {
                
                $post->setPicture3($oldPicture3);//ancien nom de fichier
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Article modifié.');
            return $this->redirectToRoute('backend_advicePostList');
        }
        return $this->render('backend/post/advicePostEdit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/advicepost/delete/{slug}", name="advicePostDelete", methods="DELETE")
     */
    public function advicePostDelete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Article supprimé.');
        }
        return $this->redirectToRoute('backend_advicePostList');
    }

    /**
     * @Route("/post/ad/delete/{slug}", name="adDelete", methods="DELETE")
     */
    public function adDelete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Annonce supprimée.');
        }
        return $this->redirectToRoute('backend_adList');
    }


    /**
     * @Route("/{id}/status/{statusCode}", name="post_update_status", methods={"PATCH"})
     */
    public function updateStatus(Request $request, Post $post, StatusRepository $statusRepository): JsonResponse
    {

        $statusCode = $request->get("statusCode");
        $newStatus = $statusRepository->findOneBy(['code' => $statusCode]);

        // 1 - On récupère le statusId fourni via l'url de la requête (Request)
        $post = $post->setStatus($newStatus);
        //On met à jour en base
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
        //On construit manuellement la réponse envoyée au navigateur (pas réussi à utiliser le module sérializer pour transformer un objet en Json)
        $toReturn = [
            'id' => $post->getId(),
            'type' => $post->getType(),
            'user' => $post->getUser(),
        ];
        //On construit une réponse json grâce à notre tableau fait-main toReturn
        $response = new JsonResponse($toReturn);
        //On l'envoie au navigateur, on peut les voir dans Network du devtool
        return $response;
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
}

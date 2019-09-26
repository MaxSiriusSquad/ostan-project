<?php

namespace App\Controller\Backend;

use App\Entity\Tag;
use App\Form\TagType;
use App\Utils\Slugger;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * 
 * @Route("/backend/", name="backend_")
 */
class TagController extends AbstractController
{
    /**
     * @Route("tag", name="tagList")
     */
    public function tagList(TagRepository $tagRepository)
    {
        return $this->render('backend/tag/tagList.html.twig', [
            'tags' => $tagRepository->findBy(array(), array('name' => 'ASC'))

        ]);
    }
    /**
     * @Route("tag/new", name="tag_new")
     */
    public function new(Request $request, Slugger $slugger) : Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $slug = $slugger->slugify($tag->getName());
            $tag->setSlug($slug);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager -> persist($tag);
            $entityManager -> flush();

            $this->addFlash(
                'success',
                'Votre tag a bien été enregistré !'
            );

            return $this->redirectToRoute('backend_tagList');
        }
       return $this->render('backend/tag/newTag.html.twig', [
           'form' => $form->createView(),
           //'slug'=> $slug
       ]);
    }

    /**
     * @Route("tag/edit/{slug}", name="tag_edit", methods="GET|POST")
     */
    public function edit(Request $request, Tag $tag, Slugger $slugger) 
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        $slug = $slugger->slugify($tag->getName());
        $tag->setSlug($slug);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Tag modifié.');
            return $this->redirectToRoute('backend_tagList');
        }
        
        return $this->render('backend/tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
            
        ]);
    }

    /**
     * @Route("tag/delete/{slug}", name="tag_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tag $tag): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tag);
            $em->flush();
            $this->addFlash('success', 'Tag supprimé.');

        }
        
        return $this->redirectToRoute('backend_tagList');
    }

}

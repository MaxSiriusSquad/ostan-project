<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;



class MainController extends AbstractController
{
   

     /**
     * @Route("/mentions-legales", name="mentions_legales")
     */
    public function legalMentions()
    {
        return $this->render('main/cgu.html.twig', [
        ]);
    }

      /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request)
    {
        $message = new Message();

            $form = $this->createForm(ContactType::class, $message);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {


                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();
                return $this->redirectToRoute('advice_post');
            }


        return $this->render('main/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/plan-du-site/", name="plan_du_site")
     */
    public function sitemap()
    {

        return $this->render('main/plan_du_site.html.twig', [
        ]);
    }

    /**
     * @Route("/le-concept/", name="concept")
     */
     public function concept()
     {


         return $this->render('main/concept.html.twig', [
         ]);
     }


}

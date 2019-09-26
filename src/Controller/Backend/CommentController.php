<?php

namespace App\Controller\Backend;

use App\Entity\Comment;
use App\Repository\StatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * 
 * @Route("/backend/", name="backend_")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("comment", name="comment")
     */
    public function index()
    {
        return $this->render('backend/comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }


    /**
     * @Route("comment/{id}/status/{statusCode}", name="comment_update_status", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function updateStatus(Request $request, Comment $comment, StatusRepository $statusRepository): JsonResponse
    {

        $statusCode = $request->get("statusCode");
        $newStatus = $statusRepository->findOneBy(['code' => $statusCode]);

        // 1 - On récupère le statusId fourni via l'url de la requête (Request)
        $comment = $comment->setStatus($newStatus);
        //On met à jour en base
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();
        //On construit manuellement la réponse envoyée au navigateur (pas réussi à utiliser le module sérializer pour transformer un objet en Json)
        $toReturn = [
            'id' => $comment->getId(),
            'user' => $comment->getUser(),
        ];
        //On construit une réponse json grâce à notre tableau fait-main toReturn
        $response = new JsonResponse($toReturn);
        //On l'envoie au navigateur, on peut les voir dans Network du devtool
        return $response;
    }
}

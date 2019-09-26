<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ExceptionController extends AbstractController
{
    /**
     * @Route("/exception", name="exception")
     */
    public function index()
    {
        return $this->render('exception/index.html.twig', [
            'controller_name' => 'ExceptionController',
        ]);
    }
}

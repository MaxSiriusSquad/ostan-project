<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Utils\Slugger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    /**
     * @Route("/tag/{slug}", name="tag_show", methods ={"GET"})
     */
    public function show(Tag $tag, Slugger $slugger)
    {
        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

}

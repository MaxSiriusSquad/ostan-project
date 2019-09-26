<?php

namespace App\Controller\Backend;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * 
 * @Route("/backend/", name="backend_")
 */
class BlockedController extends AbstractController
{
    /**
     * @Route("blocked", name="blocked_index")
     */
    public function index(PostRepository $postRepository, UserRepository $userRepository, CommentRepository $commentRepository, RoleRepository $roleRepository)
    {
        $users = $userRepository->findBy(array(), array('username' => 'ASC'));
        $comments = $commentRepository->findBy(array(), array('createdAt' => 'DESC'));
        $posts = $postRepository->findBy(array(), array('createdAt' => 'DESC'));
        $roles = $roleRepository->findAll();
        return $this->render('backend/blocked/index.html.twig', [
            'posts' => $posts,
            'users' => $users,
            'comments' => $comments,
            'roles' => $roles
        ]);
    }
}

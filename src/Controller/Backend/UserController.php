<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Utils\Slugger;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



/**
 *
 * @Route("/backend/", name="backend_")
 */
class UserController extends AbstractController
{


    /**
     * @Route("user", name="userList")
     */
    public function userList(UserRepository $userRepository, RoleRepository $roleRepository, Request $request, PaginatorInterface $paginator)
    {
        

        $roles = $roleRepository->findAll();
        $users = $this->getDoctrine()->getRepository(User::class)->findBy(array(), array('username' => 'ASC'));
        $listUser = $paginator->paginate(
            $users, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            5 // Nombre de résultats par page
        );
        return $this->render('backend/user/userList.html.twig', [
            'users' => $users,
            'roles' => $roles,
            'listUser' => $listUser
        ]);
    }

    /**
     * @Route("user/delete/{slug}", name="user_delete", methods={"DELETE","POST"})
     */
    public function delete(Request $request, User $user, Slugger $slugger): Response
    {

        if ($this->isCsrfTokenValid('delete' . $user->getSlug(), $request->request->get('_token'))) {

            $slug = $slugger->slugify($user->getUsername());
            $user->setSlug($slug);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash(
                'danger',
                'Suppression effectuée'
            );
        }

        return $this->redirectToRoute('backend_userList');
    }

    /**
     * @Route("user/edit/{slug}", name="user_edit")
     */
    public function edit(Request $request, User $user, Slugger $slugger): Response
    {
        $form = $this->createForm(TagType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugger->slugify($user->getUsername());
            $user->setSlug($slug);

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Tag modifié.');
            return $this->redirectToRoute('backend_tag_edit', ['slug' => $user->getSlug()]);
        }
        return $this->render('backend/userList.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("user/{id}/role/{roleId}", name="user_edit_role", methods={"PATCH"})
     */
    public function updateRole(Request $request, User $user, RoleRepository $roleRepository, $id): JsonResponse
    {
        // 1 - On récupère le roleId fourni via l'url de la requête (Request)
        $newRoleId = $request->get("roleId");
        //On récupère l'objet en base qui correspond à cet id (à l'id qu'on aura eu avec la requete dans l'url) pour le stocker
        $newRole = $roleRepository->findOneBy(['id' => $newRoleId]);
        // On met à jour le rôle de l'user avec le role précédemment récupéré
        $user = $user->setRole($newRole);


        //On met à jour en base
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        //On construit manuellement la réponse envoyée au navigateur (pas réussi à utiliser le module sérializer pour transformer un objet en Json)
        $toReturn = [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ];
        //On construit une réponse json grâce à notre tableau fait-main toReturn
        $response = new JsonResponse($toReturn);
        //On l'envoie au navigateur, on peut les voir dans Network du devtool
        return $response;
    }

    /**
     * @Route("user/{id}/status/{statusCode}", name="user_update_status", methods={"PATCH"})
     */
    public function updateStatus(Request $request, User $user, StatusRepository $statusRepository, $id): JsonResponse
    {
        $statusCode = $request->get("statusCode");
        $newStatus = $statusRepository->findOneBy(['code' => $statusCode]);

        // 1 - On récupère le statusId fourni via l'url de la requête (Request)
        $user = $user->setStatus($newStatus);

        //On met à jour en base
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        //On construit manuellement la réponse envoyée au navigateur (pas réussi à utiliser le module sérializer pour transformer un objet en Json)
        $toReturn = [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ];
        //On construit une réponse json grâce à notre tableau fait-main toReturn
        $response = new JsonResponse($toReturn);
        //On l'envoie au navigateur, on peut les voir dans Network du devtool
        return $response;
    }
}

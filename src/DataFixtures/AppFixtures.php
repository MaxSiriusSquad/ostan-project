<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Utils\Slugger;
use App\Repository\RoleRepository;
use Nelmio\Alice\Loader\NativeLoader;
use App\DataFixtures\MyCustomNativeLoader;
use App\Repository\StatusRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
 
    private $slugger;

    public function __construct(UserPasswordEncoderInterface $encoder, Slugger $slugger, RoleRepository $roleRepository, StatusRepository $statusRepository)
    {
      $this->encoder = $encoder; 
      $this->slugger = $slugger;  
      $this->roleRepository = $roleRepository;
      $this->statusRepository = $statusRepository;
   
    }


    public function load(ObjectManager $em )
    {

        $loader = new MyCustomNativeLoader();
        //importe le fichier de fixtures et récupère les entités générés
        $entities = $loader->loadFile(__DIR__ . '/fixtures.yml')->getObjects();
        
        /*[
        function($currentPost) {
            $slug = $this->slugger->slugify($currentPost->getTitle());
        }
        ]
        */

        foreach ($entities as $entity) {
            
            $em->persist($entity);
            
        };
        


        $em->flush();

        $role = 'ROLE_USER_ADMIN' ;
        $codeStatus = 'ROLE_USER_ADMIN' ;
        $statusCode = 'UNBLOCKED';
        $statusCode= $this->statusRepository->findOneByCode($statusCode);

        $adminRole = $this->roleRepository->findByCode($role);
        $userAdmin =new User ;
        $userAdmin->setFirstname('admin');
        $userAdmin->setLastname('admin');
        $userAdmin->setStatus($statusCode);
        $userAdmin->setSlug('mot');

        $userAdmin->setBirthdate(new \Datetime());



        $userAdmin->setUsername('admin');
        $userAdmin->setRole($adminRole[0]);
        $userAdmin->setEmail('admintest@gmail.com');
        $encodedPassword = $this->encoder->encodePassword($userAdmin, 'testadmin'); 
        $userAdmin->setPassword($encodedPassword);
        $em->persist($userAdmin);
        $em->flush();
    }
}

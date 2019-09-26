<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function searchHome($criterias)
    {
        
        return $this->createQueryBuilder('u')
            ->innerJoin('u.jobs', 'j')
            ->innerJoin('u.tags', 't')
            ->andWhere('j IN (:jobs)')
            ->andWhere('t IN (:tags)')
            ->setParameters(array(
                'jobs' => $criterias['jobs'],
                'tags' => $criterias['tags']
            ))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findJob($criterias)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.jobs', 'j')
            ->andWhere('j IN (:jobs)')
            ->setParameters(array(
                'jobs' => $criterias['jobs'],
            ))
            ->getQuery()
            ->getResult();
    }


    public function findAllExceptUser($user)
    {
    return $this->createQueryBuilder('u')
        ->andWhere('u.hidden = user')
        ->setParameter('user', $user)
        ->orderBy('u.username', 'ASC')
        ->getQuery()
        ->getResult()
    ;
    }
    
}



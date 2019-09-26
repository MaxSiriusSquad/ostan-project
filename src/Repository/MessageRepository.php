<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    //affichage des messages sur page accueil messagerie
    public function findByTitleGroup($user)
    {
        $qb = $this->createQueryBuilder('m');
        // $qb->andWhere('m.user = :currentUser')
        //     ->andWhere('m.userReceiver = :currentUser');

        $qb->where($qb->expr()->orX(
                $qb->expr()->eq('m.user', ':currentUser'),
                $qb->expr()->eq('m.userReceiver', ':currentUser')
            ))
            ->setParameter('currentUser', $user)
            ->groupBy('m.title')
            ->orderBy('m.createdAt', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }


    public function findByTitle($title)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.title = :val')
            ->setParameter('val', $title)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}

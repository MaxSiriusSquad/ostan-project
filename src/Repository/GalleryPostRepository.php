<?php

namespace App\Repository;

use App\Entity\GalleryPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GalleryPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method GalleryPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method GalleryPost[]    findAll()
 * @method GalleryPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GalleryPost::class);
    }

    // /**
    //  * @return GalleryPost[] Returns an array of GalleryPost objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GalleryPost
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

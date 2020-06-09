<?php

namespace App\Repository;

use App\Entity\Glaneur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Glaneur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Glaneur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Glaneur[]    findAll()
 * @method Glaneur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlaneurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Glaneur::class);
    }

    // /**
    //  * @return Glaneur[] Returns an array of Glaneur objects
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
    public function findOneBySomeField($value): ?Glaneur
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

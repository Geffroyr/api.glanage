<?php

namespace App\Repository;

use App\Entity\Ambassadeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ambassadeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ambassadeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ambassadeur[]    findAll()
 * @method Ambassadeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AmbassadeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ambassadeur::class);
    }

    // /**
    //  * @return Ambassadeur[] Returns an array of Ambassadeur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ambassadeur
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

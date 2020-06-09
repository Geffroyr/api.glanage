<?php

namespace App\Repository;

use App\Entity\Recuperateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recuperateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recuperateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recuperateur[]    findAll()
 * @method Recuperateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecuperateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recuperateur::class);
    }

    // /**
    //  * @return Recuperateur[] Returns an array of Recuperateur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Recuperateur
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

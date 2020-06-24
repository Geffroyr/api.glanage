<?php

namespace App\Repository;

use App\Entity\ValidateEmail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ValidateEmail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValidateEmail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValidateEmail[]    findAll()
 * @method ValidateEmail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValidateEmailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValidateEmail::class);
    }

    // /**
    //  * @return ValidateEmail[] Returns an array of ValidateEmail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ValidateEmail
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

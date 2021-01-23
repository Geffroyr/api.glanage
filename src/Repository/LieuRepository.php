<?php

namespace App\Repository;

use App\Entity\Lieu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lieu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lieu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lieu[]    findAll()
 * @method Lieu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LieuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lieu::class);
    }

    public function findBrittany()
    {
        $qb = $this->createQueryBuilder('l');
        return $qb->andWhere($qb->expr()->orX(
            $qb->expr()->like('l.codePostal',':dpt1'),
            $qb->expr()->like('l.codePostal',':dpt2'),
            $qb->expr()->like('l.codePostal',':dpt3'),
            $qb->expr()->like('l.codePostal',':dpt4')
            ))// OR l.code_postal LIKE "29"% OR l.code_postal LIKE "35"% OR l.code_postal LIKE "56"%')
            ->setParameters(['dpt1'=> '22%','dpt2'=> '29%','dpt3'=> '35%','dpt4'=> '56%'])
            ->orderBy('l.codePostal', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

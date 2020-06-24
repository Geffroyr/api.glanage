<?php

namespace App\Repository;

use App\Entity\Evenement;
use App\Entity\Lieu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Evenement::class);
    }

    public function findByDistance($glaneur) {
        $latitude_coef = Lieu::coef_latitude($glaneur->getPerimetre(), $glaneur->getLieu()->getLatitude());
        $longitude_coef = Lieu::coef_longitude($glaneur->getPerimetre(), $glaneur->getLieu()->getLatitude(), $glaneur->getLieu()->getLongitude());
        $today = new \DateTime();
        return $this->createQueryBuilder('e')
                        ->join('e.lieu', 'l')
                        ->andWhere('l.latitude < :latitude_max')
                        ->andWhere('l.latitude > :latitude_min')
                        ->andWhere('l.longitude < :longitude_max')
                        ->andWhere('l.longitude > :longitude_min')
                        ->andWhere('e.date > :today')
                        ->andWhere('e.enabled = true')
                        /* ->setParameter('latitude_max', $glaneur->getLieu()->getLatitude()+$latitude_coef)
                          ->setParameter('latitude_min', $glaneur->getLieu()->getLatitude()-$latitude_coef)
                          ->setParameter('longitude_max', $glaneur->getLieu()->getLongitude()+$longitude_coef)

                          ->setParameter('longitude_min', $glaneur->getLieu()->getLongitude()+$longitude_coef)
                         */
                        ->setParameters([
                          'today' => $today->format('Y-m-d'),
                          'latitude_max' => $glaneur->getLieu()->getLatitude() + $latitude_coef,
                          'latitude_min' => $glaneur->getLieu()->getLatitude() - $latitude_coef,
                          'longitude_max' => $glaneur->getLieu()->getLongitude() + $longitude_coef,
                          'longitude_min' => $glaneur->getLieu()->getLongitude() - $longitude_coef])
                        ->orderBy('e.id', 'ASC')
                        ->setMaxResults(10)
                        ->getQuery()
                        ->getResult();
    }
    
    // /**
    //  * @return Evenement[] Returns an array of Evenement objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('e')
      ->andWhere('e.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('e.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Evenement
      {
      return $this->createQueryBuilder('e')
      ->andWhere('e.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}

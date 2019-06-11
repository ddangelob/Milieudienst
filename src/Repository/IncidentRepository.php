<?php

namespace App\Repository;

use App\Entity\Incident;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Incident|null find($id, $lockMode = null, $lockVersion = null)
 * @method Incident|null findOneBy(array $criteria, array $orderBy = null)
 * @method Incident[]    findAll()
 * @method Incident[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncidentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Incident::class);
    }

    // /**
    //  * @return Incident[] Returns an array of Incident objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Incident
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    */
    public function getRecent($maxResult, $page)
    {
        return $this
            ->createQueryBuilder("e")
            ->orderBy("e.id", "DESC")
            ->where("e.status = 1")
            ->setMaxResults($maxResult)
            ->setFirstResult($page)
            ->getQuery()->execute();
    }
    public function getStatistics()
    {
        // Statistic queries
        $totalIncidents = $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $totalOpenIncidents = $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->where('e.status = 1')
            ->getQuery()
            ->getSingleScalarResult();
        $totalLockedIncidents = $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->where('e.status = 3')
            ->getQuery()
            ->getSingleScalarResult();


        // Make an array with all the results and return it.
        $results = [
            'total' => $totalIncidents,
            'open' => $totalOpenIncidents,
            'locked' => $totalLockedIncidents,
        ];
        return $results;
    }
    public function searchFor($searchQuery, $searchTerms){
        if(count($searchTerms === 0)){
            // Search everything because there isn't a specific table we can search in
        }

        if(count($searchTerms) > 0){
            // There is an search term so look in the given terms

        }
    }
}


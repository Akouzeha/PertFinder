<?php

namespace App\Repository;

use App\Entity\Edge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Edge>
 *
 * @method Edge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Edge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Edge[]    findAll()
 * @method Edge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EdgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Edge::class);
    }

//    /**
//     * @return Edge[] Returns an array of Edge objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Edge
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    // find all edges for a given chart
    public function findAllEdgesForChart($chartId): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e
            FROM App\Entity\Edge e
            JOIN e.task t
            WHERE t.pertChart = :chartId'
        )->setParameter('chartId', $chartId);
        
        // returns an array of Edge objects
        return $query->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Diagram;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Diagram>
 *
 * @method Diagram|null find($id, $lockMode = null, $lockVersion = null)
 * @method Diagram|null findOneBy(array $criteria, array $orderBy = null)
 * @method Diagram[]    findAll()
 * @method Diagram[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiagramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diagram::class);
    }

//    /**
//     * @return Diagram[] Returns an array of Diagram objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Diagram
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findAllTasks(Diagram $diagram): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT t
            FROM App\Entity\Task t
            WHERE t.pertChart = :diagram
            ORDER BY t.id ASC'
        )->setParameter('diagram', $diagram);
        return $query->getResult();
    }
}

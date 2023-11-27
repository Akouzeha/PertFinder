<?php

namespace App\Repository;

use App\Entity\TaskDependency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskDependency>
 *
 * @method TaskDependency|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskDependency|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskDependency[]    findAll()
 * @method TaskDependency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskDependencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskDependency::class);
    }

//    /**
//     * @return TaskDependency[] Returns an array of TaskDependency objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TaskDependency
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

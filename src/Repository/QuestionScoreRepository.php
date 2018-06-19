<?php

namespace App\Repository;

use App\Entity\QuestionScore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method QuestionScore|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionScore|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionScore[]    findAll()
 * @method QuestionScore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionScoreRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, QuestionScore::class);
    }

//    /**
//     * @return QuestionScore[] Returns an array of QuestionScore objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuestionScore
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

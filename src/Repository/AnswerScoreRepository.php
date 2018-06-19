<?php

namespace App\Repository;

use App\Entity\AnswerScore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AnswerScore|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnswerScore|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnswerScore[]    findAll()
 * @method AnswerScore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerScoreRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AnswerScore::class);
    }

//    /**
//     * @return AnswerScore[] Returns an array of AnswerScore objects
//     */
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
    public function findOneBySomeField($value): ?AnswerScore
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

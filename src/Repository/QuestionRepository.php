<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findForHomepage(){
        $qb = $this->createQueryBuilder('q')
            ->leftJoin('q.user', 'u')
            ->addOrderBy('q.creationDate', 'DESC')
            ->addOrderBy('q.title', 'ASC')
            ->addOrderBy('u.username', 'ASC')
            ->getQuery();

        return $qb->execute();
    }

    public function findForSearchbar($searchtext){
        /*
        $qb = $this->createQueryBuilder('q')
            ->leftJoin('q.user', 'u')
            ->leftJoin('q.tags', 't')
            ->andWhere('q.title LIKE :searchtext')
            ->andWhere('q.text LIKE :searchtext')
            ->andWhere('t.name LIKE :searchtext')
            ->andWhere('u.username LIKE :searchtext')
            ->addOrderBy('q.creationDate', 'ASC')
            ->addOrderBy('q.title', 'ASC')
            ->addOrderBy('u.username', 'ASC')
            ->setParameter('searchtext', $searchtext)
            ->getQuery();
        */
        $qb = $this->createQueryBuilder('q')
            ->leftJoin('q.user', 'u')
            ->leftJoin('q.tags', 't')
            ->andWhere('q.title LIKE :searchtext OR q.text LIKE :searchtext OR t.name  LIKE :searchtext OR u.username LIKE :searchtext')
            ->addOrderBy('q.creationDate', 'ASC')
            ->addOrderBy('q.title', 'ASC')
            ->addOrderBy('u.username', 'ASC')
            ->setParameter('searchtext', $searchtext)
            ->getQuery();

        return $qb->execute();



    }

    public function findForUserProfile($user){
        $qb = $this->createQueryBuilder('q')
            ->leftJoin('q.user', 'u')
            ->andWhere('u = :user')
            ->addOrderBy('q.creationDate', 'ASC')
            ->addOrderBy('q.title', 'ASC')
            ->setParameter('user', $user)
            ->getQuery();

        return $qb->execute();
    }


//    /**
//     * @return Question[] Returns an array of Question objects
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
    public function findOneBySomeField($value): ?Question
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

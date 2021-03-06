<?php

namespace App\Repository;

use App\Entity\Wish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wish[]    findAll()
 * @method Wish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wish::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Wish $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Wish $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findWishes()
    {
        $queryBuilder = $this->createQueryBuilder('w');
        $queryBuilder->leftJoin('w.category','c')
            ->addSelect('c');
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

     /**
      * @return Wish[] Returns an array of Wish objects
      */
    public function findByWithFilter($keywords, $category): array
    {
        dump($keywords);
        dump($category);
        $queryBuilder = $this->createQueryBuilder('w');
        if (!empty($category)) {
            $queryBuilder
                ->leftJoin('w.category', 'c')
                ->andWhere('w.category = :category')
                ->setParameter('category', $category);
        }
        if (!empty($keywords)) {
            $queryBuilder
                ->andWhere('w.title LIKE :keywords')
                ->setParameter('keywords', '%'.$keywords.'%');
        }
        $queryBuilder
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(100);

        return $queryBuilder->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Wish
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

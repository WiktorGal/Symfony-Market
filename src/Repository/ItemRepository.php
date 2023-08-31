<?php

namespace App\Repository;

use App\Entity\Item; 
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class); // Use the correct entity class
    }

    public function findNewestItems($limit)
    {
        return $this->createQueryBuilder('i')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findAllCategoriesWithItemCount()
    {
        return $this->createQueryBuilder('i')
            ->select('c.id', 'c.name', 'COUNT(i.id) as itemCount')
            ->join('i.category', 'c')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }

    public function findRelatedItems($category, $excludeId, $limit)
    {
        return $this->createQueryBuilder('i')
            ->where('i.category = :category')
            ->andWhere('i.isSold = :isSold')
            ->andWhere('i.id != :excludeId')
            ->setParameter('category', $category)
            ->setParameter('isSold', false)
            ->setParameter('excludeId', $excludeId)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    

}

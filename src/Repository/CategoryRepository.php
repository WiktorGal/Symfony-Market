<?php


namespace App\Repository;



use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findAllDistinctCategoriesWithItemCount()
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'COUNT(items.id) as itemCount')
            ->leftJoin('c.items', 'items')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }
}


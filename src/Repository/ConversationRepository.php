<?php

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function findByItemAndUser($item, $user)
    {
        return $this->createQueryBuilder('c')
            ->where('c.item = :item')
            ->andWhere(':user MEMBER OF c.members')
            ->setParameter('item', $item)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.members', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }   

    public function findConversationsByUser(User $user)
    {
        return $this->createQueryBuilder('c')
            ->andWhere(':user MEMBER OF c.members')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}

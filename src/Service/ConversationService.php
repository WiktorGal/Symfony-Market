<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;

class ConversationService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createConversation(Item $item, $user)
    {
        $existingConversation = $this->entityManager
            ->getRepository(Conversation::class)
            ->findOneBy(['item' => $item, 'members' => [$user, $item->getCreatedBy()]]);

        if ($existingConversation) {
            return $existingConversation;
        }

        $conversation = new Conversation();
        $conversation->setCreatedBy($user)
            ->setItem($item)
            ->addMember($user)
            ->addMember($item->getCreatedBy())
            ->setCreatedAt(new \DateTime());

        $this->entityManager->persist($conversation);
        $this->entityManager->flush();

        return $conversation;
    }
}
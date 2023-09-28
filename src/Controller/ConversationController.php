<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Item;
use App\Entity\User;
use App\Entity\Conversation;
use App\Form\ConversationMessageType;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ConversationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ConversationRepository $conversationRepository;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, ConversationRepository $conversationRepository, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->conversationRepository = $conversationRepository;
        $this->security = $security;
    }

    #[Route("/conversation/new/{itemPk}", name: "conversation_new")]
    public function newConversation(Request $request, int $itemPk): Response
    {
        // Check if the user is logged in
        if (!$this->security->isGranted('ROLE_USER')) {
            // Redirect to the login page or show an access denied message
            // For example:
            return $this->redirectToRoute('app_login');
        }

        // Find the item by its ID
        $item = $this->entityManager->getRepository(Item::class)->find($itemPk);

        if (!$item) {
            throw $this->createNotFoundException('Item not found');
        }

        // If the item was created by the current user, redirect to the dashboard
        if ($item->getCreatedBy() === $this->getUser()) {
            return $this->redirectToRoute('dashboard_index');
        }

        // Find conversations related to the item and current user
        $conversations = $this->conversationRepository->findByItemAndUser($item, $this->getUser());

        // If conversations exist, redirect to the first conversation's detail
        if ($conversations) {
            return $this->redirectToRoute('conversation_detail', ['pk' => $conversations[0]->getId(), 'itemPk' => $itemPk]);
        }

        // Create a form for sending messages
        $form = $this->createForm(ConversationMessageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Create a new conversation
            $conversation = new Conversation();
            $conversation->setItem($item);
            $conversation->setCreatedBy($this->getUser());

            // Add both the current user and the item's owner as members
            $conversation->addMember($this->getUser());
            $conversation->addMember($item->getCreatedBy());

            $now = new \DateTime();
            $conversation->setCreatedAt($now);
            $conversation->setModifiedAt($now);

            $this->entityManager->persist($conversation);

            // Create a message for the conversation
            $conversationMessage = $form->getData();
            $conversationMessage->setConversation($conversation);
            $conversationMessage->setCreatedBy($this->getUser());
            $conversationMessage->setCreatedAt($now);

            $this->entityManager->persist($conversationMessage);
            $this->entityManager->flush();

            // Fetch conversations for the user
            $conversations = $this->conversationRepository->findConversationsByUser($this->getUser());

            // Redirect to the item detail page
            return $this->redirectToRoute('item_detail', ['id' => $itemPk]);
        }

        return $this->render('conversation/new.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
            'conversations' => $conversations,
        ]);
    }

    #[Route("/conversation/{id?}", name: "inbox")]

    public function inbox(Request $request): Response
    {
        // Get the currently authenticated user
        $user = $this->getUser();
        

        // Retrieve conversations for the user
        $conversations = $this->conversationRepository->findConversationsByUser($user);

        // Debugging: Output the number of conversations retrieved
        dump($conversations);

        return $this->render('conversation/inbox.html.twig', [
            'conversations' => $conversations,
        ]);
}
}

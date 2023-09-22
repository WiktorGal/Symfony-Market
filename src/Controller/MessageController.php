<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\ConversationMessage;
use App\Form\ConversationMessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/conversation/message/send/{id}/{itemPk}", name: "message_send")]
    public function sendMessage(Request $request, int $id): Response
    {
        // Retrieve the conversation by $id
        $conversation = $this->entityManager->getRepository(Conversation::class)->find($id);

        // Check if the conversation exists
        if (!$conversation) {
            throw $this->createNotFoundException('Conversation not found');
        }

        // Create a form for sending messages
        $form = $this->createForm(ConversationMessageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $conversationMessage = $form->getData();
            $conversationMessage->setCreatedBy($user);
            $conversationMessage->setConversation($conversation);

            // Persist the message
            $this->entityManager->persist($conversationMessage);
            $this->entityManager->flush();

            // Redirect back to the conversation detail page
            return $this->redirectToRoute('conversation_detail', ['pk' => $id, 'itemPk' => $conversation->getItem()->getId()]);
        }

        // Handle form errors if any

        return $this->redirectToRoute('conversation_detail', ['pk' => $id, 'itemPk' => $conversation->getItem()->getId()]);
    }

    #[Route("/conversation/detail/{pk}/{itemPk}", name: "conversation_detail")]
    public function detail(Request $request, int $pk): Response
    {
        // Get the authenticated user
        $user = $this->getUser();

        // Fetch the conversation related to the given $pk
        $conversation = $this->entityManager->getRepository(Conversation::class)->find($pk);

        // Check if the conversation exists
        if (!$conversation) {
            throw $this->createNotFoundException('Conversation not found');
        }

        // Check if the user is a member of the conversation
        if (!$conversation->isMember($user)) {
            throw $this->createAccessDeniedException('You do not have access to this conversation');
        }

        // Create a form for sending messages
        $form = $this->createForm(ConversationMessageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conversationMessage = $form->getData();
            $conversationMessage->setCreatedBy($user);
            $conversationMessage->setConversation($conversation);

            // Persist the message
            $this->entityManager->persist($conversationMessage);
            $this->entityManager->flush();

            return $this->redirectToRoute('conversation_detail', ['pk' => $pk]);
        }

        return $this->render('conversation/detail.html.twig', [
            'conversation' => $conversation,
            'form' => $form->createView(),
        ]);
    }
}

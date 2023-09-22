<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use App\Form\EditItemType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Repository\ConversationRepository; 

class ItemController extends AbstractController
{
    private $itemRepository;
    private $entityManager;
    private $logger;
    private $doctrine;

    public function __construct(
        ItemRepository $itemRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ManagerRegistry $doctrine
    ) {
        $this->itemRepository = $itemRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    #[Route('/item/{id}', name: 'item_detail')]
    public function detail(int $id, ConversationRepository $conversationRepository): Response
    {   
        $item = $this->itemRepository->find($id);

        if (!$item) {
            throw $this->createNotFoundException('Item not found');
        }

        $relatedItems = $this->itemRepository->findRelatedItems(
            $item->getCategory(),
            $item->getId(),
            3
        );
        $conversations = $conversationRepository->findByItemAndUser($item, $this->getUser());

        return $this->render('item/index.html.twig', [
            'item' => $item,
            'related_items' => $relatedItems,
            'conversations' => $conversations,
        ]);
    }

    #[Route("/item/edit/{id}", name: "item_edit")]
    public function edit(Request $request, Item $item, int $id): Response
    {
        // Fetch the currently logged-in user
        $entityManager = $this->doctrine->getManager();
        $userId = $this->getUser()->getUserIdentifier();
        $item = $entityManager->getRepository(Item::class)->find($id);
        $itemUserEmail = $item->getCreatedBy()->getEmail();
        
        // Check if the user is the creator of the item
        if ($userId !== $itemUserEmail) {
            throw new AccessDeniedException('You are not allowed to edit this item.');
        }
    
        $form = $this->createForm(EditItemType::class, $item);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the image upload
            $imageFile = $form['imageFile']->getData();
    
            if ($imageFile) {
                // If a new image is uploaded, update the item's image
                $item->setImageFile($imageFile);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('item_detail', ['id' => $item->getId()]);
        }
    
        return $this->render('item/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit Item',
            'item' => $item,
        ]);
    }

    #[Route("/item/delete/{id}", name: "item_delete")]
    public function delete(Connection $connection, int $id): Response
    {
        // Fetch the currently logged-in user
        $entityManager = $this->doctrine->getManager();
        $userId = $this->getUser()->getUserIdentifier();
        $item = $entityManager->getRepository(Item::class)->find($id);
        $itemUserEmail = $item->getCreatedBy()->getEmail();
        // Check if the user is the creator of the item
        if ($userId !== $itemUserEmail) {
            throw new AccessDeniedException('You are not allowed to delete this item.');
        }

        // Define your DELETE SQL query
        $sql = "DELETE FROM item WHERE id = :id";
    
        try {
            // Execute the SQL query
            $connection->executeQuery($sql, ['id' => $id]);
            
            // Redirect to a route after deletion (customize this)
            return $this->redirectToRoute('app_dashboard');
        } catch (\Exception $e) {
            // Handle any exceptions or errors that may occur during deletion
            // You can log the error or display a user-friendly
        }
    }
}
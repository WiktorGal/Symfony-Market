<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Security;

class ItemCreateController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/new', name: 'item_new')]
    public function newItem(Request $request): Response
    {
        // Check if the user is logged in, otherwise, redirect to the login page
        if (!$this->security->isGranted('ROLE_USER')) {
            // You can redirect to the login page or show an access denied message here
            // For example:
            return $this->redirectToRoute('app_login');
        }

        // Get the authenticated user
        $user = $this->getUser();

        // Create a new item and set the createdBy and createdAt properties
        $item = new Item();
        $item->setCreatedBy($user);
        $item->setCreatedAt(new DateTime());

        // Create a form for item creation
        $form = $this->createForm(ItemType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission and item creation here

            // Set the imageFile property with the uploaded file
            $item->setImageFile($form->get('imageFile')->getData());

            // Persist the item
            $this->entityManager->persist($item);
            $this->entityManager->flush();

            // Redirect to the item detail page or any other page
            return $this->redirectToRoute('item_detail', ['id' => $item->getId()]);
        }

        // Render the item creation form
        return $this->render('item_create/index.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create a New Item',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface; 
use App\Entity\Item;

class DashboardController extends AbstractController
{
    private $entityManager; 

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; 
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $items = $this->entityManager->getRepository(Item::class)->findBy(['createdBy' => $this->getUser()]);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'items' => $items, 
        ]);
    }
}

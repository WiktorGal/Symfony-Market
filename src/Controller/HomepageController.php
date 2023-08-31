<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ItemRepository;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ItemRepository $itemRepository): Response
    {
        $newestItems = $itemRepository->findNewestItems(6);
        $categories = $itemRepository->findAllCategoriesWithItemCount(); // Replace with your repository method

        return $this->render('homepage/index.html.twig', [
            'items' => $newestItems,
            'categories' => $categories,
        ]);
    }
}

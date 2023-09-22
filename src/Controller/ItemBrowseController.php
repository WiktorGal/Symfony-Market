<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemBrowseController extends AbstractController
{
    #[Route("/browse", name: "item_browse")]
    public function browse(Request $request, EntityManagerInterface $entityManager): Response
    {
        $query = $request->query->get('query', '');
        $categoryRepository = $entityManager->getRepository(Category::class);
        $itemRepository = $entityManager->getRepository(Item::class);

        $categories = $categoryRepository->findAll();
        $categoryId = (int) $request->query->get('category', 0);

        $items = $itemRepository->findBy(['isSold' => false]);

        if ($categoryId) {
            $items = $itemRepository->findBy(['category' => $categoryId, 'isSold' => false]);
        }

        if (!empty($query)) {
            $items = $itemRepository->createQueryBuilder('i')
                ->where('i.name LIKE :query')
                ->orWhere('i.description LIKE :query')
                ->andWhere('i.isSold = false')
                ->setParameter('query', '%' . $query . '%')
                ->getQuery()
                ->getResult();
        }

        return $this->render('item_browse/index.html.twig', [
            'items' => $items,
            'query' => $query,
            'categories' => $categories,
            'categoryId' => $categoryId,
        ]);
    }
}

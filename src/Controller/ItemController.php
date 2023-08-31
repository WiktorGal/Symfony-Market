<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    private $itemRepository;

    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    #[Route('/item/{id}', name: 'item_detail')]
    public function detail(int $id): Response
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

        return $this->render('item/index.html.twig', [
            'item' => $item,
            'related_items' => $relatedItems,
        ]);
    }
}

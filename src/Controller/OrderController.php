<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders', name: 'order_')]
class OrderController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(OrderRepository $orderRepository, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = null;
        if($user = $this->getUser()){
            
            $paginator = $orderRepository->getOrderPaginator($user, $offset);
        }

        return $this->render('front/order/index.html.twig', [
            'orders' => $paginator,
            'offset' => OrderRepository::PAGINATOR_PER_PAGE,
            'previous' => $offset - OrderRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + OrderRepository::PAGINATOR_PER_PAGE),
        ]);
    }

        #[Route('/{id}', name: 'detail')]
        public function detail(Order $order): Response
        {

            return $this->render('front/order/detail.html.twig', [
                'order' => $order,
            ]);
        }
}

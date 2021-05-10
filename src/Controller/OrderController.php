<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders', name: 'order_')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(OrderRepository $orderRepository): Response
    {

        $orders = null;
        if($user = $this->getUser()){
            
            $orders = $orderRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
        }

        return $this->render('front/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

        #[Route('/{id}', name: 'detail')]
        public function detail(Order $order, OrderRepository $orderRepository): Response
        {

            return $this->render('front/order/detail.html.twig', [
                'order' => $order,
            ]);
        }
}

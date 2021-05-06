<?php

namespace App\Controller;

use App\Form\CartType;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CartManager $cartManager, Request $request): Response
    {
        if($user = $this->getUser()){
            $cart = $cartManager->getUserCart($user);
        } else {   
            $cart = $cartManager->getCurrentCart();
        }

        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $cart->setUpdatedAt(new \DateTime());
            $cartManager->save($cart);

            return $this->redirectToRoute('cart_index');
        }

        return $this->render('front/cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }
}

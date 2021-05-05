<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product_')]
class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'detail')]
    public function index(Product $product, Request $request, CartManager $cartManager): Response
    {
        $form = $this->createForm(AddToCartType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $item->setProducts($product);
            
            $cart = $cartManager->getCurrentCart();

            $cart
            ->addOrderHasProduct($item)
            ->setUpdatedAt(new \DateTime());

            if(!$cart->getUser() && $user = $this->getUser()){
                $cart->setUser($user);
            }
            
            $cartManager->save($cart);
            
            return $this->redirectToRoute('product_detail', ['slug' => $product->getSlug()]);
        }

        return $this->render('front/product/index.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}

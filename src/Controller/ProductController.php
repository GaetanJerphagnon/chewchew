<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products', name: 'product_')]
class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'detail')]
    public function index(Product $product, Request $request, CartManager $cartManager): Response
    {
        $form = $this->createForm(AddToCartType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $alreadyHasProduct = false;
            $currentItem = $form->getData();
            $currentItem->setProducts($product);
            
            $cart = $cartManager->getCurrentCart();
            
            // If there's no restaurant already selected (indirectly via products)
            if($currentItem->getProducts()->getRestaurant()->getId() !== $cartManager->getCurrentRestaurant()){
                $cartManager->remove($cart);
                $cart = $cartManager->getCurrentCart();
                $cart->setRestaurant($cartManager->getCurrentRestaurant());
            }
            
            foreach($cart->getOrderHasProducts() as $item){


                // If product is already in cart, if so add quantities
                if ($currentItem->getProducts() === $item->getProducts())
                {
                    $newQuantity = $currentItem->getQuantity() + $item->getQuantity();
                    $item->setQuantity($newQuantity);
                    $cart->setUpdatedAt(new \DateTime());
                    $alreadyHasProduct = true;
                }
                
            }
            
            // If product is not already in cart, add it normally
            if(!$alreadyHasProduct){
                $cart
                ->addOrderHasProduct($currentItem)
                ->setUpdatedAt(new \DateTime());
            }

            // If there's a user connected, link it to the cart
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

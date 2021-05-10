<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Factory\OrderFactory;
use App\Repository\OrderRepository;
use App\Storage\CartSessionStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class CartManager
 * @package App\Manager
 */
class CartManager
{
    /**
     * @var CartSessionStorage
     */
    private $cartSessionStorage;

    /**
     * @var OrderFactory
     */
    private $cartFactory;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $security;

    /**
     * CartManager constructor.
     *
     * @param CartSessionStorage $cartStorage
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        CartSessionStorage $cartStorage,
        EntityManagerInterface $entityManager,
        OrderFactory $orderFactory,
        OrderRepository $orderRepository,
        Security $security,
    ) {
        $this->cartSessionStorage = $cartStorage;
        $this->entityManager = $entityManager;
        $this->cartFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->security = $security;
    }

    /**
     * Gets the current cart.
     * 
     * @return Order
     */
    public function getCurrentCart(): Order
    {
        $cart = null;
        if($user = $this->security->getUser()){
            $cart = $this->orderRepository->findUserCart($user);
            
            if($cart){
                $cart = $this->cartSessionStorage->setCart($cart);
            }
        }

        if (!$cart) {
            $cart = $this->cartSessionStorage->getCart();
        }

        if (!$cart) {
            $cart = $this->cartFactory->create();
        }

        return $cart;
    }

        /**
     * Gets the current restaurant.
     * 
     * @return int|null
     */
    public function getCurrentRestaurant(): ?int
    {
        return $this->cartSessionStorage->getCartRestaurantId();
    }

    /**
     * Persists the cart in database and session.
     *
     * @param Order $cart
     */
    public function save(Order $cart): void
    {
        // Persist in database
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        // Persist in session
        $this->cartSessionStorage->setCart($cart);
    }

    /**
     * Removes the cart in database and session.
     *
     * @param Order $cart
     */
    public function remove(Order $cart): void
    {
        // Persist in database
        $this->entityManager->remove($cart);
        $this->entityManager->flush();
        // Persist in session
        $this->cartSessionStorage->removeCart();
    }
}

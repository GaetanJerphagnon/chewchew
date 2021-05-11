<?php

namespace App\Storage;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\RestaurantRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class CartSessionStorage
 * @package App\Storage
 */
class CartSessionStorage
{
    /**
     * The session storage.
     *
     * @var SessionInterface
     */
    private $session;

    /**
     * The cart repository.
     *
     * @var OrderRepository
     */
    private $cartRepository;
    
    /**
     * The cart repository.
     *
     * @var OrderRepository
     */
    private $restaurantRepository;

    /**
     * @var string
     */
    const CART_KEY_NAME = 'cart_id';
    const CART_RESTAURANT_KEY_NAME = 'restaurant_id';

    /**
     * CartSessionStorage constructor.
     *
     * @param SessionInterface $session
     * @param OrderRepository $cartRepository
     */
    public function __construct(SessionInterface $session, OrderRepository $cartRepository, RestaurantRepository $restaurantRepository ) 
    {
        $this->session = $session;
        $this->cartRepository = $cartRepository;
        $this->restaurantRepository = $restaurantRepository;
    }

    /**
     * Gets the cart in session.
     *
     * @return Order|null
     */
    public function getCart(): ?Order
    {
        return $this->cartRepository->findOneBy([
            'id' => $this->getCartId(),
            'status' => Order::STATUS_CART
        ]);
    }

    /**
     * Sets the cart in session.
     *
     * @param Order $cart
     */
    public function setCart(Order $cart): void
    {
        $this->session->set(self::CART_KEY_NAME, $cart->getId());
        if(!$this->session->get(self::CART_RESTAURANT_KEY_NAME)){
            $items = $cart->getOrderHasProducts();
            foreach($items as $item){
                $restaurantId = $item->getProducts()->getRestaurant()->getId();
                if($restaurantId !== $this->getCartRestaurantId()){

                }
                $this->setCartRestaurant($restaurantId);
                break;
            }

        }
    }

    /**
     * Sets the cart in session.
     *
     * @param Order $cart
     */
    public function setCartRestaurant($restaurantId): void
    {
        $this->session->set(self::CART_RESTAURANT_KEY_NAME, $restaurantId);
    }

    /**
     * Returns the cart id.
     *
     * @return int|null
     */
    private function getCartId(): ?int
    {
        return $this->session->get(self::CART_KEY_NAME);
    }

    /**
     * Returns the cart id.
     *
     * @return int|null
     */
    public function getCartRestaurantId(): ?int
    {
        return $this->session->get(self::CART_RESTAURANT_KEY_NAME);
    }

    public function removeCart(): void
    {
        $this->session->set(self::CART_KEY_NAME, null);
        $this->session->set(self::CART_RESTAURANT_KEY_NAME, null);
    }
}

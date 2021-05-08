<?php
namespace App\Twig;

use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\RestaurantRepository;
use App\Storage\CartSessionStorage;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $categoryRepository;
    private $orderRepository;
    private $restaurantRepository;
    private $session;

    public function __construct(
        CategoryRepository $categoryRepository,
        OrderRepository $orderRepository,
        RestaurantRepository $restaurantRepository,
        SessionInterface $session
        )
    {
        $this->categoryRepository = $categoryRepository;
        $this->orderRepository = $orderRepository;
        $this->restaurantRepository = $restaurantRepository;
        $this->session = $session;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('categories', [$this, 'getCategories']),
            new TwigFunction('topCategories', [$this, 'getTopCategories']),
            new TwigFunction('cart', [$this, 'getCart']),
            new TwigFunction('cartRestaurantId', [$this, 'getCartRestaurantId']),
            new TwigFunction('cartRestaurant', [$this, 'getCartRestaurant']),
            new TwigFunction('restaurantNumber', [$this, 'getRestaurantNumber']),
        ];
    }

    public function getCategories()
    {
        return $this->categoryRepository->findAll();
    }

    public function getTopCategories()
    {
        return $this->categoryRepository->findLast5();
    }

    public function getCart()
    {
        if($cartId = $this->session->get(CartSessionStorage::CART_KEY_NAME)){
            $cart = $this->orderRepository->findOneBy(['id' => $cartId]);
            return $cart;
        }

        return null;
    }

    public function getCartRestaurant()
    {
        if($cartRestaurantId = $this->session->get(CartSessionStorage::CART_RESTAURANT_KEY_NAME)){
            $cartRestaurant = $this->restaurantRepository->findOneBy(['id' => $cartRestaurantId]);
            return $cartRestaurant;
        }

        return null;
    }

    public function getCartRestaurantId()
    {
        if($cartRestaurantId = $this->session->get(CartSessionStorage::CART_RESTAURANT_KEY_NAME)){
            return $cartRestaurantId;
        }

        return null;
    }

    public function getRestaurantNumber():int
    {
        return count($this->restaurantRepository->findAll());
    }
}
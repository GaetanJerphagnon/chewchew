<?php
namespace App\Twig;

use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\RestaurantRepository;
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
            new TwigFunction('cart', [$this, 'getCart']),
            new TwigFunction('restaurantNumber', [$this, 'getRestaurantNumber']),
        ];
    }

    public function getCategories()
    {
        return $this->categoryRepository->findAll();
    }

    public function getCart()
    {
        if($cartId = $this->session->get('cart_id')){
            $cart = $this->orderRepository->findOneBy(['id' => $cartId]);
            dump($this->session);
            return $cart;
        }

        return null;
    }

    public function getRestaurantNumber():int
    {
        return count($this->restaurantRepository->findAll());
    }
}
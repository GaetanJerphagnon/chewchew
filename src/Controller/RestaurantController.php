<?php

namespace App\Controller;

use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/restaurants', name: 'restaurant_')]
class RestaurantController extends AbstractController
{
    #[Route('/{slug}', name: 'detail')]
    public function index(Restaurant $restaurant): Response
    {
        if (!$restaurant->getIsActive()) {
            throw $this->createNotFoundException('The restaurant does not exist');
        }

        return $this->render('front/restaurant/index.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }
}

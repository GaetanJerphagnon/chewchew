<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/', name: 'homepage')]
class HomepageController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(RestaurantRepository $restaurantRepository): Response
    {

        $restaurants = $restaurantRepository->findAll();

        return $this->render('front/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

        #[Route('/category/{slug}', name: '_category')]
        public function indexCategory(string $slug, RestaurantRepository $restaurantRepository): Response
        {
    
            $restaurants = $restaurantRepository->findByCategory($slug);
    
            return $this->render('front/index.html.twig', [
                'restaurants' => $restaurants,
            ]);
        }
}

<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/', name: 'homepage')]
class HomepageController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(RestaurantRepository $restaurantRepository, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $restaurantRepository->getRestaurantPaginator($offset);

        return $this->render('front/index.html.twig', [
            'restaurants' => $paginator,
            'offset' => RestaurantRepository::PAGINATOR_PER_PAGE,
            'previous' => $offset - RestaurantRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + RestaurantRepository::PAGINATOR_PER_PAGE),
        ]);
    }

        #[Route('/category/{slug}', name: '_category')]
        public function indexCategory(string $slug, Request $request, RestaurantRepository $restaurantRepository): Response
        {
    
            $offset = max(0, $request->query->getInt('offset', 0));
            $paginator = $restaurantRepository->findByCategory($slug, $offset);
    
            return $this->render('front/index.html.twig', [
                'restaurants' => $paginator,
                'slug' => $slug,
                'offset' => RestaurantRepository::PAGINATOR_PER_PAGE,
                'previous' => $offset - RestaurantRepository::PAGINATOR_PER_PAGE,
                'next' => min(count($paginator), $offset + RestaurantRepository::PAGINATOR_PER_PAGE),
            ]);
        }
}

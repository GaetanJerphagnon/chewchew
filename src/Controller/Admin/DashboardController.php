<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use App\Entity\Category;
use App\Entity\Menu;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Restaurant;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->renderContentMaximized()
            //->renderSidebarMinimized()
            ->setTitle('Chewchew admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-chart-bar');

        yield MenuItem::section('Read Only', 'fa fa-readme');
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Carts', 'fas fa-shopping-cart', Cart::class);
        yield MenuItem::linkToCrud('Orders', 'fas fa-money-bill-wave', Order::class);

        yield MenuItem::section('Editable entries', 'fa fa-edit');
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Restaurants', 'fas fa-utensils', Restaurant::class);
        yield MenuItem::linkToCrud('Products', 'fas fa-carrot', Product::class);
        yield MenuItem::linkToCrud('Menus', 'fas fa-concierge-bell', Menu::class);
    }
}

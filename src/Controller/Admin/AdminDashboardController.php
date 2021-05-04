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
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SubMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractDashboardController
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
        return Dashboard::new()->setTitle('<img src="logo-mini.ico"> Chewchew Administration ')->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        // ROLE_ADMIN part mostly used to view data
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-chart-bar');

        yield MenuItem::section('Read Only', 'fa fa-search');
        yield MenuItem::subMenu('Users Info', 'fa fa-info')->setSubItems([
            MenuItem::linkToCrud('Users', 'fas fa-users', User::class),
            MenuItem::linkToCrud('Carts', 'fas fa-shopping-cart', Cart::class),
            MenuItem::linkToCrud('Orders', 'fas fa-money-bill-wave', Order::class),
        ]);

        yield MenuItem::subMenu('Restaurants Info', 'fa fa-utensils')->setSubItems([
            MenuItem::linkToCrud('Restaurants', 'fas fa-store', Restaurant::class),
            MenuItem::linkToCrud('Products', 'fas fa-carrot', Product::class),
            MenuItem::linkToCrud('Menus', 'fas fa-concierge-bell', Menu::class),
        ]);

        yield MenuItem::section('Editable', 'fa fa-edit');
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);

    }
}

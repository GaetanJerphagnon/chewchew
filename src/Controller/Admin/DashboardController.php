<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderHasProducts;
use App\Entity\Product;
use App\Entity\Restaurant;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository(Order::class)->findLast10();

        return $this->render('backoffice/manager/dashboard.html.twig',[
            'user' => $this->getUser(),
            'lastOrders' => $orders,
        ]);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...

    ;
        return parent::configureUserMenu($user)
            ->setAvatarUrl($this->getParameter('user_pictures')."/".$user->getPicture())

            // you can use any type of menu item, except submenus
            ->addMenuItems([
                MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
                MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
                MenuItem::section(),
                MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ]);
    }

        /**
     * @Route("/test", name="test")
     */
    /* public function test(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository(Order::class)->findLast10();

        $banane = $em->getRepository(Product::class)->findOneBy(['name' => 'Banane']);
        $noixCoco = $em->getRepository(Product::class)->findOneBy(['name' => 'Noix de coco']);

        $order = new Order();
        $order->setUser($this->getUser())
        ->setRestaurant($em->getRepository(Restaurant::class)->findOneBy(['name' => 'Le Bananier Restau']))
        ->addOrderHasProduct(new OrderHasProducts($banane, 12))
        ->addOrderHasProduct(new OrderHasProducts($noixCoco, 5));
        
        $em->persist($order);
        $em->flush();

        return $this->render('Backoffice/Manager/dashboard.html.twig',[
            'user' => $this->getUser(),
            'lastOrders' => $orders,
        ]);
    } */

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('<img src="logo-mini.ico"> Chewchew Manager ')
                            ->disableUrlSignatures()
                            ->renderContentMaximized()

        ;
    }
    

    public function configureMenuItems(): iterable
    {
            // ROLE_RESTAURATEUR part, for them to manage their restaurant/products
            yield MenuItem::section('My informations', 'fa fa-info');
            yield MenuItem::linkToCrud('Profile', 'fas fa-user', User::class)->setAction(Action::DETAIL)->setEntityId($this->getUser()->getId());
            yield MenuItem::linkToCrud('Your Restaurants', 'fas fa-utensils', Restaurant::class);
    }
}

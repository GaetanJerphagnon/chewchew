<?php

namespace App\EventListener;

use App\Entity\Menu;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Manager\CartManager;
use App\Repository\RestaurantRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class EntityCreateSubscriber implements EventSubscriber
{

    private $cartManager;
    private $restaurantReposotiry;
    private $security;
    private $slugger;

    public function __construct(
        CartManager $cartManager,
        RestaurantRepository $restaurantRepository,
        Security $security,
        SluggerInterface $slugger
        )
    {
        $this->cartManager = $cartManager;
        $this->restaurantReposotiry = $restaurantRepository;
        $this->security = $security;
        $this->slugger = $slugger;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        /* switch ($entity) {
            case property_exists($entity, "createdAt"):
                $entity->setCreatedAt(new \Datetime("now"));
        } */
        if( property_exists($entity, "createdAt")){$entity->setCreatedAt(new \Datetime("now"));}

        if($entity instanceof User){ 
            if( property_exists($entity, "slug") ){$entity->setSlug($this->slugger->slug($entity->getFirstname()." ".$entity->getLastname()." ".$entity->getId()));}
        } else{
            if( property_exists($entity, "slug")){$entity->setSlug($this->slugger->slug($entity->getName()));}
        }

        if($entity instanceof Order){
            $this->setTotalPrice($entity);

            if($entity->getRestaurant() == null){
                // Set the restaurant id
                $items = $entity->getOrderHasProducts();
                foreach($items as $item){
                    $restaurant = $item->getProducts()->getRestaurant();
    
                    $entity->setRestaurant($restaurant);
                    break;
                }
            }
        }

        if($entity instanceof Restaurant && $user = $this->security->getUser() !== null ){
            $user = $this->security->getUser();
            $entity->setOwner($user);
        }

        if($entity instanceof Product && $user = $this->security->getUser() !== null && $_REQUEST['restaurantId']){
            $restaurant = $this->restaurantReposotiry->find($_REQUEST['restaurantId']);

            $entity->setRestaurant($restaurant);
        }

    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if( property_exists($entity, "updatedAt") ){$entity->setUpdatedAt(new \Datetime("now"));}

        if($entity instanceof User){ 
            if( property_exists($entity, "slug") ){$entity->setSlug($this->slugger->slug($entity->getFirstname()." ".$entity->getLastname()." ".$entity->getId()));}
        } else{
            if( property_exists($entity, "slug") ){$entity->setSlug($this->slugger->slug($entity->getName()));}
        }

        // Must compute total of Order 
        if($entity instanceof Order){
            $this->setTotalPrice($entity);


            if($entity->getRestaurant() == null){
                // Set the restaurant id
                $items = $entity->getOrderHasProducts();
                foreach($items as $item){
                    $restaurant = $item->getProducts()->getRestaurant();
    
                    $entity->setRestaurant($restaurant);
                    break;
                }
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // If an order is
        if($entity instanceof Order){

            // Clears session
            if($entity->getStatus() === Order::STATUS_CART && count($entity->getOrderHasProducts()) === 0){
                $this->cartManager->remove($entity);
            }
        }

    }

    public function setTotalPrice(Order $order)
    {
        $total = 0;
        if($order->getOrderHasProducts() !== null){
            $productQuantity = $order->getOrderHasProducts();
            foreach($productQuantity as $pq){
                $total += $pq->getProducts()->getPrice() * $pq->getQuantity();
            }
        }
        if($order->getMenus() !== null){
            $menus = $order->getMenus();
            foreach($menus as $m){
                $total += $m->getPrice();
            }
        }
        $order->setTotal($total);
    }

/*     public function 
 */

}
<?php

namespace App\Form\EventListener;

use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class OrderCartItemsListener implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'postSubmit'];
    }

    /**
     * Removes all items from the cart when the clear button is clicked.
     *
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $cart = $form->getData();

        if (!$cart instanceof Order) {
            return;
        }

        // Is the clear button clicked?
        if (!$form->get('orderHasProducts')->isSubmitted()) {
            return;
        }

        if($cart->getRestaurant() == null){

            // Set the restaurant id
            $items = $cart->getOrderHasProducts();
            foreach($items as $item){
                $restaurant = $item->getProducts()->getRestaurant();

                $cart->setRestaurant($restaurant);
                break;
            }
        }
    }
}

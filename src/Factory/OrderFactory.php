<?php

namespace App\Factory;

use App\Entity\Order;
use App\Entity\OrderHasProducts;
use App\Entity\Product;

/**
 * Class OrderFactory
 * @package App\Factory
 */
class OrderFactory
{
    /**
     * Creates an order.
     *
     * @return Order
     */
    public function create(): Order
    {
        $order = new Order();
        $order
            ->setStatus(Order::STATUS_CART)
            ->setCreatedAt(new \DateTime());

        return $order;
    }

    /**
     * Creates an item for a product.
     *
     * @param Product $product
     *
     * @return OrderHasProducts
     */
    public function createItem(Product $product): OrderHasProducts
    {
        $item = new OrderHasProducts();
        $item->setProducts($product);
        $item->setQuantity(1);

        return $item;
    }
}

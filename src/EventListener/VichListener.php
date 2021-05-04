<?php

namespace App\EventListener;

use App\Entity\Product;
use App\Entity\Restaurant;
use Vich\UploaderBundle\Event\Event;

class VichListener
{
    public function onVichUploaderPreRemove(Event $event)
    {
        $object = $event->getObject();
        $explodedArray = explode("\\", get_class($object));
        $lowerEntityName = strtolower($explodedArray[array_key_last($explodedArray)]);

        if( $object->getPicture() == "default-".$lowerEntityName.".png" ) {
            // This prevents Vich from deleting default gift image, reseted value in controller before flush
            $object->setPicture(null);
        }
    }

    public function onVichUploaderPostRemove(Event $event)
    {
        $object = $event->getObject();
        
        if( $object->getPicture() == null ){
            // Get Entity name in lowercase, ex: "product", "restaurant"..
            $explodedArray = explode("\\", get_class($object));
            $lowerEntityName = strtolower($explodedArray[array_key_last($explodedArray)]);

            // Only to use this for reset the default picture
            $object->setPicture("default-".$lowerEntityName.".png");
        }
    }

}
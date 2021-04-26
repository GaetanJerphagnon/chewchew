<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class EntityCreateSubscriber implements EventSubscriber
{

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
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
    }


}
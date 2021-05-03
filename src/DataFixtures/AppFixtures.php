<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderHasProducts;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $em)
    {
        $loader = new CustomNativeLoader();
        
        //importe le fichier de fixtures et récupère les entités générés
        $entities = $loader->loadFile(__DIR__ . '/fixtures.yaml')->getObjects();
        
        //empile la liste d'objet à enregistrer en BDD
        foreach ($entities as $entity) {
            $em->persist($entity);
        };

        // On créer l'admin
        $user = new User();
        $user->setEmail('admin@gmail.com')
            ->setPassword(password_hash('1234', PASSWORD_DEFAULT))
            ->setFirstname('Gaetan')
            ->setLastname('Jerphagnon')
            ->setAddress('19 B Rue des Archives 59800 Lille')
            ->setSlug('gaetan-jerphagnon')
            ->setBirthday( new \DateTime('now'))
            ->setPictureUrl('gaetan.jpeg')
            ->setRoles(array("ROLE_ADMIN"));
         $em->persist($user);
        
        //enregistre
        $em->flush();
    }
}

<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator as FakerGenerator;
use Nelmio\Alice\Faker\Provider\AliceProvider;
use Nelmio\Alice\Loader\NativeLoader;

class CustomNativeLoader extends NativeLoader
{
    protected function createFakerGenerator(): FakerGenerator
    {
        // Possibilté d'indiquer la langue utilisée par Faker
        $generator = Factory::create('fr_FR');
        // Ajout du provider habituel
        $generator->addProvider(new AliceProvider());

        return $generator;
    }
}

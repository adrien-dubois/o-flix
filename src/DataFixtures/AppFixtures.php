<?php

namespace App\DataFixtures;

use App\Entity\Character;
use App\Entity\TvShow;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create();
        $fakers = \Faker\Factory::create();
        $faker->addProvider(new \Xylis\FakerCinema\Provider\TvShow($faker));
        $fakers->addProvider(new \Xylis\FakerCinema\Provider\Character($fakers));
        $fake = \Faker\Factory::create();
        $fake->addProvider(new \Xylis\FakerCinema\Provider\Person($fake));


        
        for ($i=0; $i <5 ; $i++) { 

            $tvShow = new TvShow();
            $tvShow->setTitle($faker->tvShow);
            $tvShow->setSynopsis($faker->overview);
            $tvShow->setNbLikes(15);
            $tvShow->setImage('01.png');

            $gender = mt_rand(0, 1) ? 'male' : 'female';
            $fullNameArray = explode(" ", $fakers->character($gender));

            // On créé un personnage vide
            $character = new Character();
            $character->setFirstname($fullNameArray[0]);
            $character->setLastname($fullNameArray[1] ?? ' Doe' . $i);
            $character->setGender($gender == 'male' ? 'Homme' : 'Femme');
            $character->setTruename($fake->actor);

            $manager->persist($character);
            $manager->persist($tvShow);

        }

        // We save datas
        $manager->flush();
    }
}

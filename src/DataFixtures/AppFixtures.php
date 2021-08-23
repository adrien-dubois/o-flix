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
        $faker->addProvider(new \Xylis\FakerCinema\Provider\TvShow($faker));
        
        for ($i=0; $i <5 ; $i++) { 

            $tvShow = new TvShow();
            $tvShow->setTitle($faker->tvShow);
            $tvShow->setSynopsis($faker->overview);
            $tvShow->setNbLikes(15);
            $tvShow->setImage('01.png');
            $tvShow->setCreatedAt(new DateTimeImmutable());

            $manager->persist($tvShow);

        }

        // We save datas
        $manager->flush();
    }
}

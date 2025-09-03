<?php

namespace App\DataFixtures;

use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $game1 = new Game();
        $game1->setTitle('FIFA 23');
        $game1->setPrice(69.99);
        $manager->persist($game1);

        $game2 = new Game();
        $game2->setTitle('The Witcher 3');
        $game2->setPrice(39.99); 
        $manager->persist($game2);

        $manager->flush();
    }
}

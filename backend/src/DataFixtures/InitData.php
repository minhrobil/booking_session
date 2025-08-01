<?php

namespace App\DataFixtures;

use App\Entity\SessionType;
use App\Entity\Trainer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class InitData extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $type = new SessionType();
        $type->setName('Tennis');
        $type->setDuration(60);
        $type->setPrice('10.00');
        $manager->persist($type);

        $type = new SessionType();
        $type->setName('Padel');
        $type->setDuration(120);
        $type->setPrice('20.00');
        $manager->persist($type);

        $type = new SessionType();
        $type->setName('Fitness');
        $type->setDuration(180);
        $type->setPrice('30.00');
        $manager->persist($type);

        //trainer
        $trainer = new Trainer();
        $trainer->setName('John');
        $manager->persist($trainer);

        $trainer = new Trainer();
        $trainer->setName('Alex');
        $manager->persist($trainer);

        $trainer = new Trainer();
        $trainer->setName('Max');
        $manager->persist($trainer);

        $manager->flush();
    }
}

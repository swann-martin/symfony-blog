<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }



    public function load(ObjectManager $manager)
    {
        // $product = new Product();


        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, "password{$i}");

            $user->setEmail("user{$i}@user.com")
                ->setPassword($password);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

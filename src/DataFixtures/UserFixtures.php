<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User2;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User2();
        $user->setUsername('user1');
        $user->setFullname('Vasya Petrov');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            '123'
        ));
        $manager->persist($user);

        $user = new User2();
        $user->setUsername('user2');
        $user->setFullname('Vanya Ivanov');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            '456'
        ));
        $manager->persist($user);

        $manager->flush();
    }
}


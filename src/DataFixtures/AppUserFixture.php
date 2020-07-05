<?php

namespace App\DataFixtures;

use App\Entity\SiteUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppUserFixture extends Fixture
{
    public const DEFAULT_EMAIL = 'user@example.com';
    public const DEFAULT_PASSWORD = 'example-password';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new SiteUser())
            ->setEmail(self::DEFAULT_EMAIL)
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setPassword($this->passwordEncoder->encodePassword($user, self::DEFAULT_PASSWORD));

        $manager->persist($user);
        $manager->flush();
    }
}

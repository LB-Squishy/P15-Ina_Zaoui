<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $description = "Le maître de l'urbanité capturée, explore les méandres des cités avec un regard vif et impétueux, figeant l'énergie des rues dans des instants éblouissants. À travers une technique avant-gardiste, il métamorphose le béton et l'acier en toiles abstraites, révélant l'essence même de l'architecture moderne. Ses clichés transcendent les formes familières pour révéler des perspectives inattendues, offrant une vision nouvelle et captivante du monde urbain.";

        // Création du compte d'Ina Zaoui
        $ina = new User();
        $ina->setSuperAdmin(true);
        $ina->setAdmin(true);
        $ina->setName('Ina Zaoui');
        $ina->setEmail('ina@zaoui.com');
        $ina->setDescription(null);
        $ina->setPassword($this->userPasswordHasher->hashPassword($ina, 'password'));
        $manager->persist($ina);

        $this->addReference('user_ina', $ina);

        // Création de 100 autres utilisateurs
        foreach (range(1, 100) as $i) {
            $user = new User();
            $user->setSuperAdmin(false);
            $user->setAdmin(true);
            $user->setName(sprintf('Invité %d', $i));
            $user->setEmail(sprintf('invite+%d@example.com', $i));
            $user->setDescription($description);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
            $manager->persist($user);

            $this->addReference(sprintf('user_guest%d', $i), $user);
        }

        $manager->flush();
    }
}

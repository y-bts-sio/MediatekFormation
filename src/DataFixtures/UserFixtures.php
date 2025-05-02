<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Classe UserFixtures
 * 
 * Cette fixture crée un utilisateur administrateur pour accéder au back-office.
 */
class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface Service de hachage de mot de passe
     */
    private UserPasswordHasherInterface $hasher;

    /**
     * Constructeur
     *
     * @param UserPasswordHasherInterface $hasher Service d'encodage des mots de passe
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Charge un utilisateur admin avec un mot de passe sécurisé.
     *
     * @param ObjectManager $manager Gestionnaire d'entités
     */
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@mediatek.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->hasher->hashPassword($admin, 'admin')
        );

        $manager->persist($admin);
        $manager->flush();
    }
}

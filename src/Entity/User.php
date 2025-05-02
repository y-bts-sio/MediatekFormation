<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Entité représentant un utilisateur de l'application.
 * 
 * Cette entité est utilisée pour l'authentification de l’administrateur.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identifiant unique de l'utilisateur.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Adresse email de l'utilisateur.
     */
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * Rôles de l'utilisateur (par défaut : ROLE_USER).
     * 
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe haché de l'utilisateur.
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Récupère l'identifiant unique.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère l'adresse email.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse email.
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Identifiant visuel utilisé dans l'application.
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Récupère les rôles attribués à l'utilisateur.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur.
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Récupère le mot de passe haché.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe haché.
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Efface les informations sensibles en mémoire (optionnel).
     */
    public function eraseCredentials(): void
    {
        // Ex : $this->plainPassword = null;
    }
}

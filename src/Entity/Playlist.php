<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une playlist contenant des formations.
 */
#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Formation>
     */
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'playlist')]
    private Collection $formations;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setPlaylist($this);
        }
        return $this;
    }

    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation) && $formation->getPlaylist() === $this) {
            $formation->setPlaylist(null);
        }
        return $this;
    }

    /**
     * Retourne la liste unique des catégories liées via les formations.
     *
     * @return Collection<int, string>
     */
    public function getCategoriesPlaylist(): Collection
    {
        $categories = new ArrayCollection();
        foreach ($this->formations as $formation) {
            foreach ($formation->getCategories() as $categorie) {
                if (!$categories->contains($categorie->getName())) {
                    $categories[] = $categorie->getName();
                }
            }
        }
        return $categories;
    }

    /**
     * Retourne le nombre de formations dans la playlist.
     */
    public function getFormationCount(): int
    {
        return count($this->formations);
    }
}

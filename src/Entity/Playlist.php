<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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

    // Retourne l'ID de la playlist
    public function getId(): ?int
    {
        return $this->id;
    }

    // Retourne le nom de la playlist
    public function getName(): ?string
    {
        return $this->name;
    }

    // Définit le nom de la playlist
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    // Retourne la description de la playlist
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Définit la description de la playlist
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

    // Ajoute une formation à la playlist
    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setPlaylist($this);
        }

        return $this;
    }

    // Supprime une formation de la playlist
    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getPlaylist() === $this) {
                $formation->setPlaylist(null);
            }
        }

        return $this;
    }
    
    /**
     * @return Collection<int, string>
     */
    public function getCategoriesPlaylist() : Collection
    {
        $categories = new ArrayCollection();
        foreach($this->formations as $formation) {
            $categoriesFormation = $formation->getCategories();
            foreach($categoriesFormation as $categorieFormation) {
                if (!$categories->contains($categorieFormation->getName())) {
                    $categories->add($categorieFormation->getName());
                }
            }
        }
        return $categories;
    }
}
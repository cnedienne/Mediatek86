<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, Formation>
     */
    #[ORM\ManyToMany(targetEntity: Formation::class, mappedBy: 'categories')]
    private Collection $formations;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    // Retourne l'ID de la catégorie
    public function getId(): ?int
    {
        return $this->id;
    }

    // Retourne le nom de la catégorie
    public function getName(): ?string
    {
        return $this->name;
    }

    // Définit le nom de la catégorie
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    // Retourne les formations associées à la catégorie
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    // Ajoute une formation à la catégorie
    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->addCategory($this);
        }

        return $this;
    }

    // Supprime une formation de la catégorie
    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            $formation->removeCategory($this);
        }
    }
}
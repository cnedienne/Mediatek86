<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    // Début de chemin vers les images
    private const CHEMIN_IMAGE = "https://i.ytimg.com/vi/";
        
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $publishedAt = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $videoId = null;

    #[ORM\ManyToOne(inversedBy: 'formations')]
    private ?Playlist $playlist = null;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'formations')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    // Getter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour la date de publication
    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    // Setter pour la date de publication
    public function setPublishedAt(?\DateTimeInterface $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    // Méthode pour obtenir la date de publication sous forme de chaîne
    public function getPublishedAtString(): string {
        if($this->publishedAt == null){
            return "";
        }
        return $this->publishedAt->format('d/m/Y');     
    }      
    
    // Getter pour le titre
    public function getTitle(): ?string
    {
        return $this->title;
    }

    // Setter pour le titre
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    // Getter pour la description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Setter pour la description
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    // Getter pour l'ID de la vidéo
    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    // Setter pour l'ID de la vidéo
    public function setVideoId(?string $videoId): static
    {
        $this->videoId = $videoId;

        return $this;
    }

    // Méthode pour obtenir la miniature de la vidéo
    public function getMiniature(): ?string
    {
        return self::CHEMIN_IMAGE.$this->videoId."/default.jpg";
    }

    // Méthode pour obtenir l'image de la vidéo
    public function getPicture(): ?string
    {
        return self::CHEMIN_IMAGE.$this->videoId."/hqdefault.jpg";
    }
    
    // Getter pour la playlist
    public function getPlaylist(): ?playlist
    {
        return $this->playlist;
    }

    // Setter pour la playlist
    public function setPlaylist(?Playlist $playlist): static
    {
        $this->playlist = $playlist;

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    // Getter pour les catégories
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    // Méthode pour ajouter une catégorie
    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    // Méthode pour supprimer une catégorie
    public function removeCategory(Categorie $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }
}

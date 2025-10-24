<?php

namespace App\Entity;

use App\Repository\OuvrageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OuvrageRepository::class)]
class Ouvrage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 125)]
    private ?string $titre = null;

    #[ORM\Column(length: 125, nullable: true)]
    private ?string $editeur = null;

    #[ORM\Column(length: 125)]
    private ?string $ISBN = null;

    #[ORM\Column]
    private ?int $annÃee = null;

    #[ORM\Column(length: 512)]
    private ?string $resume = null;

    /**
     * @var Collection<int, categorie>
     */
    #[ORM\ManyToMany(targetEntity: categorie::class, inversedBy: 'categories')]
    private Collection $categories;

    /**
     * @var Collection<int, auteur>
     */
    #[ORM\ManyToMany(targetEntity: auteur::class, inversedBy: 'auteurs')]
    private Collection $auteurs;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->auteurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->editeur;
    }

    public function setEditeur(?string $editeur): static
    {
        $this->editeur = $editeur;

        return $this;
    }

    public function getISBN(): ?string
    {
        return $this->ISBN;
    }

    public function setISBN(string $ISBN): static
    {
        $this->ISBN = $ISBN;

        return $this;
    }

    public function getAnnÃee(): ?int
    {
        return $this->annÃee;
    }

    public function setAnnÃee(int $annÃee): static
    {
        $this->annÃee = $annÃee;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * @return Collection<int, categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(categorie $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, auteur>
     */
    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function addAuteur(auteur $auteur): static
    {
        if (!$this->auteurs->contains($auteur)) {
            $this->auteurs->add($auteur);
        }

        return $this;
    }

    public function removeAuteur(auteur $auteur): static
    {
        $this->auteurs->removeElement($auteur);

        return $this;
    }
}

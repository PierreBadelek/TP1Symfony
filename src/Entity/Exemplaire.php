<?php

namespace App\Entity;

use App\Repository\ExemplaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExemplaireRepository::class)]
class Exemplaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $cote = null;

    #[ORM\Column(length: 16)]
    private ?string $etat = null;

    #[ORM\Column]
    private ?bool $disponible = null;

    #[ORM\ManyToOne(inversedBy: 'exemplaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ouvrage $Ouvrage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCote(): ?int
    {
        return $this->cote;
    }

    public function setCote(int $cote): static
    {
        $this->cote = $cote;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function isDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): static
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getOuvrage(): ?Ouvrage
    {
        return $this->Ouvrage;
    }

    public function setOuvrage(?Ouvrage $Ouvrage): static
    {
        $this->Ouvrage = $Ouvrage;

        return $this;
    }
}

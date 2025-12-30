<?php

namespace App\Entity;

use App\Repository\EmpruntRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'utilisateur doit être défini')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'exemplaire doit être défini')]
    private ?Exemplaire $exemplaire = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La date d\'emprunt doit être définie')]
    #[Assert\LessThanOrEqual('today', message: 'La date d\'emprunt ne peut pas être dans le futur')]
    private ?\DateTimeInterface $dateEmprunt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La date de retour prévue doit être définie')]
    #[Assert\GreaterThan(propertyPath: 'dateEmprunt', message: 'La date de retour doit être après la date d\'emprunt')]
    private ?\DateTimeInterface $dateRetourPrevue = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'dateEmprunt', message: 'La date de retour effective doit être après la date d\'emprunt')]
    private ?\DateTimeInterface $dateRetourEffective = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le statut ne peut pas être vide')]
    #[Assert\Choice(choices: ['en_cours', 'termine', 'en_retard'], message: 'Le statut doit être: en_cours, termine ou en_retard')]
    private ?string $statut = 'en_cours';

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateRappelJ3 = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateRappelJ0 = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateRappelJ7 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getExemplaire(): ?Exemplaire
    {
        return $this->exemplaire;
    }

    public function setExemplaire(?Exemplaire $exemplaire): static
    {
        $this->exemplaire = $exemplaire;
        return $this;
    }

    public function getDateEmprunt(): ?\DateTimeInterface
    {
        return $this->dateEmprunt;
    }

    public function setDateEmprunt(\DateTimeInterface $dateEmprunt): static
    {
        $this->dateEmprunt = $dateEmprunt;
        return $this;
    }

    public function getDateRetourPrevue(): ?\DateTimeInterface
    {
        return $this->dateRetourPrevue;
    }

    public function setDateRetourPrevue(\DateTimeInterface $dateRetourPrevue): static
    {
        $this->dateRetourPrevue = $dateRetourPrevue;
        return $this;
    }

    public function getDateRetourEffective(): ?\DateTimeInterface
    {
        return $this->dateRetourEffective;
    }

    public function setDateRetourEffective(?\DateTimeInterface $dateRetourEffective): static
    {
        $this->dateRetourEffective = $dateRetourEffective;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDateRappelJ3(): ?\DateTimeInterface
    {
        return $this->dateRappelJ3;
    }

    public function setDateRappelJ3(?\DateTimeInterface $dateRappelJ3): static
    {
        $this->dateRappelJ3 = $dateRappelJ3;
        return $this;
    }

    public function getDateRappelJ0(): ?\DateTimeInterface
    {
        return $this->dateRappelJ0;
    }

    public function setDateRappelJ0(?\DateTimeInterface $dateRappelJ0): static
    {
        $this->dateRappelJ0 = $dateRappelJ0;
        return $this;
    }

    public function getDateRappelJ7(): ?\DateTimeInterface
    {
        return $this->dateRappelJ7;
    }

    public function setDateRappelJ7(?\DateTimeInterface $dateRappelJ7): static
    {
        $this->dateRappelJ7 = $dateRappelJ7;
        return $this;
    }

    public function isEnRetard(): bool
    {
        if ($this->dateRetourEffective) {
            return false; // Déjà retourné
        }
        return $this->dateRetourPrevue < new \DateTime();
    }

    public function getJoursRetard(): int
    {
        if (!$this->isEnRetard()) {
            return 0;
        }
        $now = new \DateTime();
        return $now->diff($this->dateRetourPrevue)->days;
    }
}

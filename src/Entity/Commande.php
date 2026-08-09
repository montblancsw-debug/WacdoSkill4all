<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $commande_statut = null;

    #[ORM\Column]
    private ?\DateTime $commande_date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $commande_total = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeStatut(): ?string
    {
        return $this->commande_statut;
    }

    public function setCommandeStatut(string $commande_statut): static
    {
        $this->commande_statut = $commande_statut;

        return $this;
    }

    public function getCommandeDate(): ?\DateTime
    {
        return $this->commande_date;
    }

    public function setCommandeDate(\DateTime $commande_date): static
    {
        $this->commande_date = $commande_date;

        return $this;
    }

    public function getCommandeTotal(): ?string
    {
        return $this->commande_total;
    }

    public function setCommandeTotal(string $commande_total): static
    {
        $this->commande_total = $commande_total;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}

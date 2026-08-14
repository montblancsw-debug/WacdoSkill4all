<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    // Identifiant unique de la commande.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Le statut de la commande est obligatoire.
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(
        message: 'Le statut de la commande est obligatoire.'
    )]
    #[Assert\Choice(
        choices: [
            'en attente',
            'préparée',
            'livrée',
        ],
        message: 'Le statut sélectionné est invalide.'
    )]
    private ?string $commande_statut = null;

    // La date de la commande est obligatoire.
    #[ORM\Column]
    #[Assert\NotNull(
        message: 'La date de la commande est obligatoire.'
    )]
    private ?\DateTime $commande_date = null;

    // Le total de la commande doit être positif.
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(
        message: 'Le total de la commande est obligatoire.'
    )]
    #[Assert\Positive(
        message: 'Le total de la commande doit être supérieur à 0 €.'
    )]
    private ?string $commande_total = null;

    // Une commande peut conserver son historique sans utilisateur.
    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $utilisateur = null;

    /**
     * @var Collection<int, Detail>
     */
    #[ORM\OneToMany(
        targetEntity: Detail::class,
        mappedBy: 'commande'
    )]
    private Collection $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeStatut(): ?string
    {
        return $this->commande_statut;
    }

    public function setCommandeStatut(?string $commande_statut): static
    {
        $this->commande_statut = $commande_statut;

        return $this;
    }

    public function getCommandeDate(): ?\DateTime
    {
        return $this->commande_date;
    }

    public function setCommandeDate(?\DateTime $commande_date): static
    {
        $this->commande_date = $commande_date;

        return $this;
    }

    public function getCommandeTotal(): ?string
    {
        return $this->commande_total;
    }

    public function setCommandeTotal(?string $commande_total): static
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

    /**
     * @return Collection<int, Detail>
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(Detail $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
            $detail->setCommande($this);
        }

        return $this;
    }

    public function removeDetail(Detail $detail): static
    {
        if ($this->details->removeElement($detail)) {
            if ($detail->getCommande() === $this) {
                $detail->setCommande(null);
            }
        }

        return $this;
    }
}
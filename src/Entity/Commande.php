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
// ====================================================== //
// ===================== PROPRIETES. ==================== //
// ====================================================== //

// Identifiant unique de la commande.

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

// Statut actuel de la commande.

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le statut de la commande est obligatoire.')]
    #[Assert\Choice(choices: ['en attente','en préparation','prête','en livraison','livrée','annulée',],message: 'Le statut sélectionné est invalide.')]
    private ?string $commande_statut = null;

// Date et heure de la commande.

    #[ORM\Column]
    #[Assert\NotNull(
        message: 'La date de la commande est obligatoire.'
    )]
    private ?\DateTime $commande_date = null;

// Montant total de la commande.

    #[ORM\Column(type: Types::DECIMAL,precision: 10,scale: 2)]
    #[Assert\NotBlank(message: 'Le total de la commande est obligatoire.')]
    #[Assert\PositiveOrZero(message: 'Le total de la commande ne peut pas être négatif.')]
    private ?string $commande_total = null;

// Utilisateur associé à la commande.

    #[ORM\ManyToOne(inversedBy: 'commandes')]

// L'utilisateur peut être supprimé sans supprimer la commande.

    #[ORM\JoinColumn(nullable: true,onDelete: 'SET NULL')]
    private ?Utilisateur $utilisateur = null;

// Lignes contenant les articles de la commande.

    #[ORM\OneToMany(targetEntity: Detail::class,mappedBy: 'commande')]
    private Collection $details;

// ====================================================== //
// ==================== CONSTRUCTEUR. =================== //
// ====================================================== //

// Initialise la collection des détails de la commande.

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }

// ====================================================== //
// ================== GETTERS/SETTERS. ================== //
// ====================================================== //

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeStatut(): ?string
    {
        return $this->commande_statut;
    }

    public function setCommandeStatut(
        ?string $commande_statut
    ): static {
        $this->commande_statut = $commande_statut;

        return $this;
    }

    public function getCommandeDate(): ?\DateTime
    {
        return $this->commande_date;
    }

    public function setCommandeDate(
        ?\DateTime $commande_date
    ): static {
        $this->commande_date = $commande_date;

        return $this;
    }

    public function getCommandeTotal(): ?string
    {
        return $this->commande_total;
    }

    public function setCommandeTotal(
        ?string $commande_total
    ): static {
        $this->commande_total = $commande_total;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(
        ?Utilisateur $utilisateur
    ): static {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getDetails(): Collection
    {
        return $this->details;
    }

// Ajoute une ligne à la commande.

    public function addDetail(Detail $detail): static {
        if (!$this->details->contains($detail)) {$this->details->add($detail);$detail->setCommande($this);}
        return $this;
    }

// Retire une ligne de la commande.

    public function removeDetail(Detail $detail): static {if ($this->details->removeElement($detail)) {if ($detail->getCommande() === $this) {$detail->setCommande(null);
            }
        }
        return $this;
    }

// Vérifie si le client peut encore modifier la commande.

    public function isModifiableByClient(): bool
    {
        return in_array($this->commande_statut,['en attente','en préparation',],true);
    }

// Vérifie si le client peut encore annuler la commande.

    public function isAnnulableByClient(): bool
    {
        return in_array($this->commande_statut,['en attente','en préparation','prête',],true);
    }
}
<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $utilisateur_nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $utilisateur_prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $utilisateur_email = null;

    #[ORM\Column(length: 72)]
    private ?string $utilisateur_password = null;

    #[ORM\Column(length: 255)]
    private ?string $utilisateur_role = null;

    // Un utilisateur peut être associé à plusieurs commandes.

    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'utilisateur')]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateurNom(): ?string
    {
        return $this->utilisateur_nom;
    }

    public function setUtilisateurNom(string $utilisateur_nom): static
    {
        $this->utilisateur_nom = $utilisateur_nom;

        return $this;
    }

    public function getUtilisateurPrenom(): ?string
    {
        return $this->utilisateur_prenom;
    }

    public function setUtilisateurPrenom(?string $utilisateur_prenom): static
    {
        $this->utilisateur_prenom = $utilisateur_prenom;

        return $this;
    }

    public function getUtilisateurEmail(): ?string
    {
        return $this->utilisateur_email;
    }

    public function setUtilisateurEmail(string $utilisateur_email): static
    {
        $this->utilisateur_email = $utilisateur_email;

        return $this;
    }

    public function getUtilisateurPassword(): ?string
    {
        return $this->utilisateur_password;
    }

    public function setUtilisateurPassword(string $utilisateur_password): static
    {
        $this->utilisateur_password = $utilisateur_password;

        return $this;
    }

    public function getUtilisateurRole(): ?string
    {
        return $this->utilisateur_role;
    }

    public function setUtilisateurRole(string $utilisateur_role): static
    {
        $this->utilisateur_role = $utilisateur_role;

        return $this;
    }

    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getUtilisateur() === $this) {
                $commande->setUtilisateur(null);
            }
        }

        return $this;
    }
}
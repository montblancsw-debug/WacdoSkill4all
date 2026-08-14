<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(
    fields: ['utilisateur_email'],
    message: 'Cette adresse email est déjà utilisée.'
)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Identifiant unique de l'utilisateur.
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Le nom de l'utilisateur est obligatoire.
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le nom de l\'utilisateur est obligatoire.',
        normalizer: 'trim'
    )]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $utilisateur_nom = null;

    // Le prénom reste facultatif.
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $utilisateur_prenom = null;

    // L'adresse email sert également d'identifiant de connexion.
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'L\'adresse email est obligatoire.',
        normalizer: 'trim'
    )]
    #[Assert\Email(
        message: 'L\'adresse email "{{ value }}" n\'est pas valide.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/',
        message: 'L\'adresse email doit contenir un @ et une extension valide comme .fr ou .com.'
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: 'L\'adresse email ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $utilisateur_email = null;

    // Contient uniquement le mot de passe haché.
    #[ORM\Column(length: 72)]
    private ?string $utilisateur_password = null;

    // Le rôle détermine les droits dans le back-office.
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le rôle de l\'utilisateur est obligatoire.'
    )]
    #[Assert\Choice(
        choices: [
            'ROLE_ADMIN',
            'ROLE_PREPARATEUR',
            'ROLE_ACCUEIL',
        ],
        message: 'Le rôle sélectionné est invalide.'
    )]
    private ?string $utilisateur_role = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(
        targetEntity: Commande::class,
        mappedBy: 'utilisateur'
    )]
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

    public function setUtilisateurNom(?string $utilisateur_nom): static
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

    public function setUtilisateurEmail(?string $utilisateur_email): static
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

    public function setUtilisateurRole(?string $utilisateur_role): static
    {
        $this->utilisateur_role = $utilisateur_role;

        return $this;
    }

    // Retourne l'adresse email utilisée pour la connexion Symfony.
    public function getUserIdentifier(): string
    {
        return (string) $this->utilisateur_email;
    }

    // Retourne le rôle Symfony de l'utilisateur.
    public function getRoles(): array
    {
        if ($this->utilisateur_role === null) {
            return [];
        }

        return [
            $this->utilisateur_role,
        ];
    }

    // Retourne le mot de passe haché à Symfony.
    public function getPassword(): ?string
    {
        return $this->utilisateur_password;
    }

    // Supprime les éventuelles données sensibles temporaires.
    public function eraseCredentials(): void {}

    /**
     * @return Collection<int, Commande>
     */
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

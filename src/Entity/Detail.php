<?php

namespace App\Entity;

use App\Repository\DetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/*
 * Entité Detail du projet Wacdo.
 *
 * Cahier des charges :
 * Detail fait le lien entre une commande
 * et les produits commandés.
 *
 * Il permet notamment de conserver :
 * - la quantité commandée ;
 * - le prix unitaire au moment de la commande ;
 * - le total de la ligne ;
 * - la commande concernée ;
 * - le produit concerné lorsqu'il existe encore.
 */
#[ORM\Entity(repositoryClass: DetailRepository::class)]
class Detail
{
    /*
     * Identifiant unique de la ligne de commande.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /*
     * Quantité du produit commandé.
     */
    #[ORM\Column]
    private ?int $detail_quantite = null;

    /*
     * Prix unitaire du produit au moment
     * où la commande a été effectuée.
     *
     * Cette information est conservée même
     * si le prix du produit change ensuite.
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $detail_unitaire = null;

    /*
     * Une ligne Detail appartient obligatoirement
     * à une commande.
     */
    #[ORM\ManyToOne(inversedBy: 'details')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    /*
     * Relation Detail -> Produit.
     *
     * IMPORTANT POUR WACDO :
     *
     * un produit peut être supprimé du catalogue,
     * mais l'historique des anciennes commandes
     * doit être conservé.
     *
     * La relation devient donc nullable.
     *
     * ON DELETE SET NULL signifie :
     *
     * suppression d'un Produit
     *          ↓
     * le Detail reste en base
     *          ↓
     * produit_id devient NULL
     *
     * Les autres produits et commandes
     * ne sont jamais supprimés.
     */
    #[ORM\ManyToOne(inversedBy: 'details')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Produit $produit = null;

    /*
     * Total de la ligne de commande.
     *
     * Exemple :
     * quantité 2 × prix unitaire 8,50 €
     * = total de ligne 17,00 €.
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $detail_total = null;

    /*
     * Retourne l'identifiant du détail.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /*
     * Retourne la quantité commandée.
     */
    public function getDetailQuantite(): ?int
    {
        return $this->detail_quantite;
    }

    /*
     * Modifie la quantité commandée.
     */
    public function setDetailQuantite(int $detail_quantite): static
    {
        $this->detail_quantite = $detail_quantite;

        return $this;
    }

    /*
     * Retourne le prix unitaire enregistré
     * au moment de la commande.
     */
    public function getDetailUnitaire(): ?string
    {
        return $this->detail_unitaire;
    }

    /*
     * Modifie le prix unitaire.
     */
    public function setDetailUnitaire(string $detail_unitaire): static
    {
        $this->detail_unitaire = $detail_unitaire;

        return $this;
    }

    /*
     * Retourne la commande associée.
     */
    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    /*
     * Associe le détail à une commande.
     */
    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }

    /*
     * Retourne le produit associé.
     *
     * Peut retourner NULL lorsqu'un produit
     * historique a été supprimé du catalogue.
     */
    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    /*
     * Associe un produit au détail.
     *
     * NULL est autorisé afin de conserver
     * l'historique lorsqu'un produit est supprimé.
     */
    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    /*
     * Retourne le montant total de la ligne.
     */
    public function getDetailTotal(): ?string
    {
        return $this->detail_total;
    }

    /*
     * Modifie le montant total de la ligne.
     */
    public function setDetailTotal(string $detail_total): static
    {
        $this->detail_total = $detail_total;

        return $this;
    }
}
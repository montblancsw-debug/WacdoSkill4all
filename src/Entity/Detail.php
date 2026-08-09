<?php

namespace App\Entity;

use App\Repository\DetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailRepository::class)]
class Detail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $detail_quantite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $detail_unitaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDetailQuantite(): ?int
    {
        return $this->detail_quantite;
    }

    public function setDetailQuantite(int $detail_quantite): static
    {
        $this->detail_quantite = $detail_quantite;

        return $this;
    }

    public function getDetailUnitaire(): ?string
    {
        return $this->detail_unitaire;
    }

    public function setDetailUnitaire(string $detail_unitaire): static
    {
        $this->detail_unitaire = $detail_unitaire;

        return $this;
    }
}

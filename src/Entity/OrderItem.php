<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Order $orderItem = null;

    #[ORM\Column(length: 255)]
    private ?string $gameId = null;

    #[ORM\Column(length: 255)]
    private ?string $gameTitle = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderItem(): ?Order
    {
        return $this->orderItem;
    }

    public function setOrderItem(?Order $orderItem): static
    {
        $this->orderItem = $orderItem;

        return $this;
    }

    public function getGameId(): ?string
    {
        return $this->gameId;
    }

    public function setGameId(?string $gameId): static
    {
        $this->gameId = $gameId;

        return $this;
    }

    public function getGameTitle(): ?string
    {
        return $this->gameTitle;
    }

    public function setGameTitle(?string $gameTitle): static
    {
        $this->gameTitle = $gameTitle;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}

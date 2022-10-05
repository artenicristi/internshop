<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $productCode;

    #[ORM\Column]
    #[Assert\Positive]
    private int $amount;

    #[ORM\Column(length: 255)]
    #[Assert\Positive]
    private string $price;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'items')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Order $orderLink;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): self
    {
        $this->productCode = $productCode;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOrderLink(): ?Order
    {
        return $this->orderLink;
    }

    public function setOrderLink(?Order $orderLink): self
    {
        $this->orderLink = $orderLink;

        return $this;
    }
}

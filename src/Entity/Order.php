<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\PersistentCollection;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ORM\HasLifecycleCallbacks()]
class Order
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in-progress';
    const STATUS_SHIPPING = 'shipping';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELED = 'canceled';

    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_IN_PROGRESS,
        self::STATUS_SHIPPING,
        self::STATUS_CLOSED,
        self::STATUS_CANCELED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $total = null;

    #[ORM\Column(length: 255)]
    private ?string $status = self::STATUS_NEW;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Assert\Valid]
    #[ORM\Embedded(class: Address::class, columnPrefix: false)]
    private Address $address;

    #[ORM\Embedded(class: PaymentDetails::class, columnPrefix: false)]
    private PaymentDetails $paymentDetails;

    #[Assert\Valid]
    #[ORM\Embedded(class: CreditCardDetails::class, columnPrefix: false)]
    private ?CreditCardDetails $creditCardDetails;

    #[ORM\OneToMany(mappedBy: 'orderLink', targetEntity: OrderItem::class, cascade: ['persist'])]
    private ArrayCollection|PersistentCollection $items;

    #[ORM\ManyToOne(targetEntity:User::class, inversedBy:"orders")]
    private $user;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    #[ORM\PrePersist]
    public function recalculateTotal()
    {
        $total = array_reduce($this->items->toArray(), function (int $total, OrderItem $item) {
            $total += $item->getPrice() * $item->getAmount();
            return $total;
        }, 0);
        $this->total = $total;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getItems(): ArrayCollection|PersistentCollection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrderLink($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrderLink() === $this) {
                $item->setOrderLink(null);
            }
        }

        return $this;
    }

    public function getPaymentDetails(): PaymentDetails
    {
        return $this->paymentDetails;
    }

    public function setPaymentDetails(PaymentDetails $paymentDetails): self
    {
        $this->paymentDetails = $paymentDetails;
        return $this;
    }

    public function getCreditCardDetails(): ?CreditCardDetails
    {
        return $this->creditCardDetails;
    }

    public function setCreditCardDetails(?CreditCardDetails $creditCardDetails): void
    {
        $this->creditCardDetails = $creditCardDetails;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

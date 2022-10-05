<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class PaymentDetails
{
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?CreditCardDetails $card = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCard(): ?CreditCardDetails
    {
        return $this->card;
    }

    public function setCard(?CreditCardDetails $card): self
    {
        $this->card = $card;

        return $this;
    }
}

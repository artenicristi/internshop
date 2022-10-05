<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class CreditCardDetails
{
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\CardScheme(
        schemes: [Assert\CardScheme::VISA],
        message: 'Your credit card number is invalid.',
    )]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 3, max: 255)]
    #[Assert\Regex(pattern: '/^( {0,}\d {0,}){3}$/', message: 'Please enter a valid CVV code.')]
    private ?string $cvv = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Length(min: 2, max: 5)]
    #[Assert\Regex(pattern: '/^((0[1-9])|(1[0-2]))\/\d{2}$/', message: 'Please enter a valid date.')]
    private ?string $expiresAt = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    public function setCvv(?string $cvv): self
    {
        $this->cvv = $cvv;

        return $this;
    }

    public function getExpiresAt(): ?string
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?string $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}

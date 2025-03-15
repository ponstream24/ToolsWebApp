<?php

namespace Domain\ValueObjects\Totp;

use Domain\Exceptions\InvalidTotpIssuerException;

class TotpIssuer
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidTotpIssuerException('TOTP発行者は空にできません。');
        }
        if (mb_strlen($value) > 100) {
            throw new InvalidTotpIssuerException('TOTP発行者は100文字以内である必要があります。');
        }
        if (strpos($value, ':') !== false) {
            throw new InvalidTotpIssuerException('TOTP発行者にコロン(:)を含めることはできません。');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
} 
<?php

namespace Domain\ValueObjects\Totp;

use Domain\Exceptions\InvalidTotpSecretException;

class TotpSecret
{
    private string $value;

    public function __construct(string $value)
    {
        if (!$this->isValidBase32($value)) {
            throw new InvalidTotpSecretException('Invalid TOTP secret format');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    private function isValidBase32(string $value): bool
    {
        return (bool)preg_match('/^[A-Z2-7]+={0,6}$/', $value);
    }
} 
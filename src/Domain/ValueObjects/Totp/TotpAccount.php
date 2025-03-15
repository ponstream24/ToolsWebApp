<?php

namespace Domain\ValueObjects\Totp;

use Domain\Exceptions\InvalidTotpAccountException;

class TotpAccount
{
    private string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidTotpAccountException('Account must be a valid email address');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
} 
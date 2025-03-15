<?php

namespace Domain\ValueObjects\Password;

use Domain\Exceptions\InvalidPasswordLengthException;

class PasswordLength
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 8 || $value > 128) {
            throw new InvalidPasswordLengthException('パスワードの長さは8から128文字の間である必要があります。');
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }
} 
<?php

namespace Domain\Exceptions;

class InvalidTotpSecretException extends \Exception
{
    public function __construct(string $message = "Invalid TOTP secret", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 
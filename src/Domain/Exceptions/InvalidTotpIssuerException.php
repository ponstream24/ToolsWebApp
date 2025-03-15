<?php

namespace Domain\Exceptions;

class InvalidTotpIssuerException extends \Exception
{
    public function __construct(string $message = "Invalid TOTP issuer", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 
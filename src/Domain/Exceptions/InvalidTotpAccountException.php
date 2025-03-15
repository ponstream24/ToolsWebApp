<?php

namespace Domain\Exceptions;

class InvalidTotpAccountException extends \Exception
{
    public function __construct(string $message = "Invalid TOTP account", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 
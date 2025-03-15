<?php

namespace Domain\Exceptions;

class InvalidPasswordCharacterSetException extends \InvalidArgumentException
{
    public function __construct(string $message = "無効なパスワード文字セットです。", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 
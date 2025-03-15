<?php

namespace Domain\Exceptions;

class EncoderException extends \Exception
{
    public function __construct(string $message = "エンコード/デコード処理中にエラーが発生しました。", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 
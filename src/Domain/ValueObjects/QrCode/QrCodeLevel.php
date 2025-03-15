<?php

namespace Domain\ValueObjects\QrCode;

use Endroid\QrCode\ErrorCorrectionLevel;

class QrCodeLevel
{
    private ErrorCorrectionLevel $value;

    public function __construct(ErrorCorrectionLevel $value)
    {
        $this->value = $value;
    }

    public function value(): ErrorCorrectionLevel
    {
        return $this->value;
    }
} 
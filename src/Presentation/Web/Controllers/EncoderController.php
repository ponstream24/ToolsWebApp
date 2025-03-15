<?php

namespace Presentation\Web\Controllers;

use Application\Services\EncoderApplicationService;

class EncoderController
{
    private EncoderApplicationService $encoderService;

    public function __construct(EncoderApplicationService $encoderService)
    {
        $this->encoderService = $encoderService;
    }

    public function index(): void
    {
        require_once __DIR__ . '/../Views/tools/encoder/index.php';
    }
} 
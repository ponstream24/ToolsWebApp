<?php

namespace Presentation\Web\Controllers;

use Application\Services\QrCodeApplicationService;

class QrController
{
    private QrCodeApplicationService $qrCodeService;

    public function __construct(QrCodeApplicationService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index(): void
    {
        require_once __DIR__ . '/../Views/tools/qr/index.php';
    }

    public function generate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $text = $_POST['text'] ?? '';
            $size = (int)($_POST['size'] ?? 300);
            $level = $_POST['level'] ?? 'L';
            
            $result = $this->qrCodeService->generateQrCode($text, $size, $level);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
    }
} 
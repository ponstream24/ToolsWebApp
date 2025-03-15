<?php

namespace Presentation\Web\Controllers;

use Application\Services\PasswordApplicationService;

class PasswordController
{
    private PasswordApplicationService $passwordService;

    public function __construct(PasswordApplicationService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    public function index(): void
    {
        require_once __DIR__ . '/../Views/tools/pass/index.php';
    }

    public function generate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $length = (int)($_POST['length'] ?? 12);
            $useUppercase = isset($_POST['uppercase']);
            $useLowercase = isset($_POST['lowercase']);
            $useNumbers = isset($_POST['numbers']);
            $useSymbols = isset($_POST['symbols']);
            
            $result = $this->passwordService->generatePassword(
                $length,
                $useUppercase,
                $useLowercase,
                $useNumbers,
                $useSymbols
            );
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
    }
} 
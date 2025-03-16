<?php

namespace Presentation\Web\Controllers;

use Application\Services\TotpApplicationService;
use Infrastructure\Logging\Logger;
use Monolog\Logger as MonologLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TotpController
{
    /** @var TotpApplicationService */
    private $totpService;
    
    /** @var MonologLogger */
    private $logger;

    public function __construct(TotpApplicationService $totpService)
    {
        $this->totpService = $totpService;
        $this->logger = Logger::getInstance();
    }

    public function index(): void
    {
        require_once __DIR__ . '/../Views/tools/totp/index.php';
    }

    public function generate(): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->logger->warning('Invalid request method for TOTP generation', [
                'method' => $_SERVER['REQUEST_METHOD']
            ]);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid request method'
            ]);
            return;
        }

        $this->logger->info('TOTP generation request received', [
            'secret' => $_POST['secret'] ?? null,
            'issuer' => $_POST['issuer'] ?? 'WebTools',
            'account' => $_POST['account'] ?? 'user@example.com'
        ]);

        $result = $this->totpService->generateTotpUri(
            $_POST['secret'] ?? null,
            $_POST['issuer'] ?? 'WebTools',
            $_POST['account'] ?? 'user@example.com'
        );

        if ($result['success']) {
            $this->logger->info('TOTP generation successful', [
                'secret' => $result['secret']
            ]);
        } else {
            $this->logger->error('TOTP generation failed', [
                'error' => $result['error']
            ]);
        }

        echo json_encode($result);
    }

    public function generateQr(): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->logger->warning('Invalid request method for TOTP QR generation', [
                'method' => $_SERVER['REQUEST_METHOD']
            ]);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid request method'
            ]);
            return;
        }

        try {
            $uri = $_POST['uri'] ?? '';
            if (empty($uri)) {
                $this->logger->warning('Empty URI provided for TOTP QR generation');
                echo json_encode([
                    'success' => false,
                    'error' => 'URI is required'
                ]);
                return;
            }

            $this->logger->info('TOTP QR generation request received', [
                'uri' => $uri
            ]);

            $qrCodeService = new \Domain\Services\QrCodeService();
            $qrCode = $qrCodeService->generateQrCode($uri);

            $this->logger->info('TOTP QR generation successful');

            echo json_encode([
                'success' => true,
                'data' => $qrCode
            ]);
        } catch (\Exception $e) {
            $this->logger->error('TOTP QR generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            echo json_encode([
                'success' => false,
                'error' => 'QRコードの生成に失敗しました: ' . $e->getMessage()
            ]);
        }
    }

    public function verify(): string
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->logger->warning('Invalid request method for TOTP verification');
                return $this->jsonResponse(false, 'Invalid request method');
            }

            $postData = $_POST;
            if (empty($postData)) {
                parse_str(file_get_contents('php://input'), $postData);
            }

            $secret = $postData['secret'] ?? '';
            if (empty($secret)) {
                $this->logger->warning('Empty secret provided for TOTP verification');
                return $this->jsonResponse(false, 'Secret is required');
            }

            $this->logger->info('TOTP verification request received', ['secret' => $secret]);

            try {
                $code = $this->totpService->generateCode($secret);
                $this->logger->info('TOTP code generated successfully', ['code' => $code]);
                return $this->jsonResponse(true, ['code' => $code]);
            } catch (\InvalidArgumentException $e) {
                $this->logger->warning('Invalid TOTP secret', [
                    'error' => $e->getMessage()
                ]);
                return $this->jsonResponse(false, $e->getMessage());
            } catch (\Exception $e) {
                $this->logger->error('Error generating TOTP code', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return $this->jsonResponse(false, 'Failed to generate TOTP code');
            }
        } catch (\Exception $e) {
            $this->logger->error('Unexpected error in TOTP verification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->jsonResponse(false, 'サーバーエラーが発生しました。');
        }
    }

    private function jsonResponse(bool $success, $data): string
    {
        header('Content-Type: application/json');
        if ($success) {
            return json_encode([
                'success' => true,
                'data' => $data
            ]);
        } else {
            return json_encode([
                'success' => false,
                'error' => $data
            ]);
        }
    }
}
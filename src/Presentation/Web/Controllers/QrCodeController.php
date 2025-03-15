<?php

namespace Presentation\Web\Controllers;

use Domain\Services\QrCodeService;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;
use Infrastructure\Logging\Logger;

class QrCodeController
{
    /** @var QrCodeService */
    private $qrCodeService;
    
    /** @var Logger */
    private $logger;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
        $this->logger = Logger::getInstance();
    }

    public function index(): void
    {
        $viewPath = __DIR__ . '/../Views/tools/qr/index.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException('ビューファイルが見つかりません: ' . $viewPath);
        }
        require $viewPath;
    }

    public function generate(): string
    {
        try {
            $this->logger->info('QRコード生成リクエストを受信しました', [
                'post_data' => $_POST,
                'raw_input' => file_get_contents('php://input')
            ]);

            $postData = $_POST;
            if (empty($postData)) {
                parse_str(file_get_contents('php://input'), $postData);
            }

            $this->logger->info('解析されたPOSTデータ', [
                'parsed_data' => $postData
            ]);

            $type = $postData['type'] ?? 'text';
            $size = isset($postData['size']) ? (int)$postData['size'] : 300;
            $margin = isset($postData['margin']) ? (int)$postData['margin'] : 10;
            $errorCorrection = $postData['errorCorrection'] ?? 'medium';

            // データの組み立て
            $data = $this->buildData($type, $postData);

            $this->logger->info('ビルドされたデータ', [
                'type' => $type,
                'data' => $data
            ]);

            if (empty($data)) {
                $this->logger->warning('QRコード生成に必要なデータが入力されていません', [
                    'type' => $type,
                    'post_data' => $postData
                ]);
                return $this->jsonResponse(false, 'データが入力されていません。');
            }

            // QRコードの生成
            $qrCode = $this->qrCodeService->generateQrCode($data, $size, $margin, $errorCorrection);

            $this->logger->info('QRコードの生成に成功しました', [
                'type' => $type,
                'data' => $data,
                'size' => $size,
                'margin' => $margin,
                'errorCorrection' => $errorCorrection
            ]);

            return $this->jsonResponse(true, '', [
                'data' => $qrCode
            ]);
        } catch (\Exception $e) {
            $this->logger->error('QRコード生成中にエラーが発生しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->jsonResponse(false, 'サーバーエラーが発生しました: ' . $e->getMessage());
        }
    }

    private function buildData(string $type, array $data): string
    {
        switch ($type) {
            case 'text':
                return $data['text-data'] ?? '';
            case 'url':
                return $data['url-data'] ?? '';
            case 'tel':
                $tel = $data['tel-data'] ?? '';
                return $tel ? "tel:$tel" : '';
            case 'wifi':
                $ssid = $data['ssid'] ?? '';
                $password = $data['password'] ?? '';
                $encryption = $data['encryption'] ?? 'WPA';
                return $ssid && $password ? "WIFI:T:$encryption;S:$ssid;P:$password;;" : '';
            case 'mailto':
                $email = $data['email'] ?? '';
                return $email ? "mailto:$email" : '';
            case 'geo':
                $lat = $data['lat'] ?? '';
                $lon = $data['lon'] ?? '';
                return ($lat && $lon) ? "geo:$lat,$lon" : '';
            default:
                return '';
        }
    }

    private function getErrorCorrectionLevel(string $level)
    {
        switch (strtolower($level)) {
            case 'medium':
                return new ErrorCorrectionLevelMedium();
            case 'quartile':
                return new ErrorCorrectionLevelQuartile();
            case 'high':
                return new ErrorCorrectionLevelHigh();
            case 'low':
            default:
                return new ErrorCorrectionLevelLow();
        }
    }

    private function parseColor(string $color): array
    {
        return [
            'r' => hexdec(substr($color, 1, 2)),
            'g' => hexdec(substr($color, 3, 2)),
            'b' => hexdec(substr($color, 5, 2)),
            'a' => 0
        ];
    }

    private function jsonResponse(bool $success, string $error = '', array $data = []): string
    {
        header('Content-Type: application/json');
        return json_encode(array_merge(
            ['success' => $success],
            $error ? ['error' => $error] : $data
        ));
    }
} 
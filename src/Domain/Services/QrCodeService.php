<?php

namespace Domain\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Infrastructure\Logging\Logger;

class QrCodeService
{
    /** @var Logger */
    private $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    public function generateQrCode(string $text, int $size = 300, int $margin = 10, string $errorCorrection = 'medium'): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle($size, $margin),
                new SvgImageBackEnd()
            );

            $writer = new Writer($renderer);
            $svgData = $writer->writeString($text);

            $this->logger->info('QRコードを生成しました', [
                'text' => $text,
                'size' => $size,
                'margin' => $margin,
                'errorCorrection' => $errorCorrection
            ]);

            return 'data:image/svg+xml;base64,' . base64_encode($svgData);
        } catch (\Exception $e) {
            $this->logger->error('QRコード生成中にエラーが発生しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function getErrorCorrectionLevel(string $level): string
    {
        switch (strtolower($level)) {
            case 'medium':
                return 'M';
            case 'quartile':
                return 'Q';
            case 'high':
                return 'H';
            case 'low':
            default:
                return 'L';
        }
    }
} 
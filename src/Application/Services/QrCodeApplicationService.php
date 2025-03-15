<?php

namespace Application\Services;

use Domain\Services\QrCodeService;
use Domain\ValueObjects\QrCode\{
    QrCodeText,
    QrCodeSize,
    QrCodeLevel
};
use Domain\Exceptions\{
    InvalidQrCodeTextException,
    InvalidQrCodeSizeException,
    InvalidQrCodeLevelException
};

class QrCodeApplicationService
{
    private QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function generateQrCode(string $text, int $size, string $level): array
    {
        try {
            $qrCodeText = new QrCodeText($text);
            $qrCodeSize = new QrCodeSize($size);
            $qrCodeLevel = new QrCodeLevel($level);

            $qrCodeImage = $this->qrCodeService->generate(
                $qrCodeText,
                $qrCodeSize,
                $qrCodeLevel
            );

            return [
                'success' => true,
                'data' => base64_encode($qrCodeImage)
            ];
        } catch (InvalidQrCodeTextException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (InvalidQrCodeSizeException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (InvalidQrCodeLevelException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'QRコードの生成中にエラーが発生しました。'
            ];
        }
    }
} 
<?php

namespace Application\Services;

use Domain\Services\TotpService;
use Domain\ValueObjects\Totp\{
    TotpSecret,
    TotpIssuer,
    TotpAccount
};
use Domain\Exceptions\{
    InvalidTotpSecretException,
    InvalidTotpIssuerException,
    InvalidTotpAccountException
};
use Infrastructure\Logging\Logger;
use Monolog\Logger as MonologLogger;

class TotpApplicationService
{
    /** @var TotpService */
    private $totpService;
    
    /** @var MonologLogger */
    private $logger;

    public function __construct(TotpService $totpService)
    {
        $this->totpService = $totpService;
        $this->logger = Logger::getInstance();
    }

    public function generateTotpUri(?string $secret = null, string $issuer = 'WebTools', string $account = 'user@example.com'): array
    {
        return $this->totpService->generateTotpUri($secret, $issuer, $account);
    }

    public function generateCode(string $secret): string
    {
        return $this->totpService->generateCode($secret);
    }

    private function generateSecret(): string
    {
        $bytes = random_bytes(20);
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
} 
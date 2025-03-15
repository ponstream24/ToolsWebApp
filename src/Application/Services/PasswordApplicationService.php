<?php

namespace Application\Services;

use Domain\Services\PasswordService;
use Domain\ValueObjects\Password\{
    PasswordLength,
    PasswordCharacterSet
};
use Domain\Exceptions\{
    InvalidPasswordLengthException,
    InvalidPasswordCharacterSetException
};

class PasswordApplicationService
{
    private PasswordService $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    public function generatePassword(
        int $length,
        bool $useUppercase,
        bool $useLowercase,
        bool $useNumbers,
        bool $useSymbols
    ): array {
        try {
            $passwordLength = new PasswordLength($length);
            $characterSet = new PasswordCharacterSet(
                $useUppercase,
                $useLowercase,
                $useNumbers,
                $useSymbols
            );

            $password = $this->passwordService->generate(
                $passwordLength,
                $characterSet
            );

            return [
                'success' => true,
                'data' => $password
            ];
        } catch (InvalidPasswordLengthException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (InvalidPasswordCharacterSetException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'パスワードの生成中にエラーが発生しました。'
            ];
        }
    }
} 
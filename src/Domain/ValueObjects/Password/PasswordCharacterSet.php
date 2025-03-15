<?php

namespace Domain\ValueObjects\Password;

use Domain\Exceptions\InvalidPasswordCharacterSetException;

class PasswordCharacterSet
{
    private string $characters = '';
    private array $requiredCharacters = [];
    
    private const UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
    private const NUMBERS = '0123456789';
    private const SYMBOLS = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    public function __construct(
        bool $useUppercase,
        bool $useLowercase,
        bool $useNumbers,
        bool $useSymbols
    ) {
        if (!$useUppercase && !$useLowercase && !$useNumbers && !$useSymbols) {
            throw new InvalidPasswordCharacterSetException('少なくとも1つの文字セットを選択する必要があります。');
        }

        if ($useUppercase) {
            $this->characters .= self::UPPERCASE;
            $this->requiredCharacters[] = self::UPPERCASE[random_int(0, strlen(self::UPPERCASE) - 1)];
        }
        if ($useLowercase) {
            $this->characters .= self::LOWERCASE;
            $this->requiredCharacters[] = self::LOWERCASE[random_int(0, strlen(self::LOWERCASE) - 1)];
        }
        if ($useNumbers) {
            $this->characters .= self::NUMBERS;
            $this->requiredCharacters[] = self::NUMBERS[random_int(0, strlen(self::NUMBERS) - 1)];
        }
        if ($useSymbols) {
            $this->characters .= self::SYMBOLS;
            $this->requiredCharacters[] = self::SYMBOLS[random_int(0, strlen(self::SYMBOLS) - 1)];
        }
    }

    public function value(): string
    {
        return $this->characters;
    }

    public function getRequiredCharacters(): array
    {
        return $this->requiredCharacters;
    }
} 
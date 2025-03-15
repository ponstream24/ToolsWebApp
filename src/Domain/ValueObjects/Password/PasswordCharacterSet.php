<?php

namespace Domain\ValueObjects\Password;

use Domain\Exceptions\InvalidPasswordCharacterSetException;

class PasswordCharacterSet
{
    private bool $uppercase;
    private bool $lowercase;
    private bool $numbers;
    private bool $symbols;

    /**
     * @param bool $uppercase 大文字を使用するか
     * @param bool $lowercase 小文字を使用するか
     * @param bool $numbers 数字を使用するか
     * @param bool $symbols 記号を使用するか
     * @throws InvalidPasswordCharacterSetException 文字セットが無効な場合
     */
    public function __construct(bool $uppercase, bool $lowercase, bool $numbers, bool $symbols)
    {
        $this->uppercase = $uppercase;
        $this->lowercase = $lowercase;
        $this->numbers = $numbers;
        $this->symbols = $symbols;

        if (!$this->isValid()) {
            throw new InvalidPasswordCharacterSetException('少なくとも1つの文字セットを選択してください。');
        }
    }

    /**
     * 文字セットが有効かどうかを確認
     * 少なくとも1つの文字セットが選択されている必要がある
     *
     * @return bool
     */
    private function isValid(): bool
    {
        return $this->uppercase || $this->lowercase || $this->numbers || $this->symbols;
    }

    /**
     * 大文字を使用するかどうか
     *
     * @return bool
     */
    public function useUppercase(): bool
    {
        return $this->uppercase;
    }

    /**
     * 小文字を使用するかどうか
     *
     * @return bool
     */
    public function useLowercase(): bool
    {
        return $this->lowercase;
    }

    /**
     * 数字を使用するかどうか
     *
     * @return bool
     */
    public function useNumbers(): bool
    {
        return $this->numbers;
    }

    /**
     * 記号を使用するかどうか
     *
     * @return bool
     */
    public function useSymbols(): bool
    {
        return $this->symbols;
    }
} 
<?php

namespace Infrastructure\Persistence;

use Domain\Entities\Tool;
use Domain\Repositories\IToolRepository;

class InMemoryToolRepository implements IToolRepository
{
    /**
     * @var Tool[]
     */
    private array $tools = [];

    public function __construct()
    {
        // ハードコードされたツールデータを初期化
        $this->tools = [
            new Tool(
                1,
                'QRコードジェネレーター',
                'QRコードを生成できます。テキスト、URL、Wi-Fi設定など様々なデータタイプに対応しています。',
                '/tool/qr',
                'generator'
            ),
            new Tool(
                2,
                'パスワード生成ツール',
                '安全で強力なパスワードを生成できます。長さや文字種類をカスタマイズ可能です。',
                '/tool/password',
                'security'
            ),
            new Tool(
                3,
                'TOTP認証',
                '二段階認証用のTOTP（時間ベースのワンタイムパスワード）を生成できます。',
                '/tool/totp',
                'security'
            )
        ];
    }

    /**
     * @return Tool[]
     */
    public function findAll(): array
    {
        return $this->tools;
    }

    public function findById(int $id): ?Tool
    {
        foreach ($this->tools as $tool) {
            if ($tool->getId() === $id) {
                return $tool;
            }
        }
        return null;
    }

    /**
     * @return Tool[]
     */
    public function findByCategory(string $category): array
    {
        return array_filter(
            $this->tools,
            fn (Tool $tool) => $tool->getCategory() === $category
        );
    }

    public function save(Tool $tool): void
    {
        $found = false;

        foreach ($this->tools as $key => $existingTool) {
            if ($existingTool->getId() === $tool->getId()) {
                $this->tools[$key] = $tool;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->tools[] = $tool;
        }
    }

    public function delete(int $id): void
    {
        $this->tools = array_filter(
            $this->tools,
            fn (Tool $tool) => $tool->getId() !== $id
        );
    }
} 
<?php

namespace Presentation\Web\Controllers;

use Application\Services\ToolService;

class ToolController
{
    private ToolService $toolService;

    public function __construct(ToolService $toolService)
    {
        $this->toolService = $toolService;
    }

    public function index(): void
    {
        $tools = $this->toolService->getAllTools();
        $categories = [
            'security' => [
                'name' => 'セキュリティ',
                'icon' => 'fa-shield-alt',
                'description' => '暗号化、パスワード生成など、セキュリティ関連のツール群です。',
            ],
            'converter' => [
                'name' => '変換',
                'icon' => 'fa-exchange-alt',
                'description' => '様々なフォーマット間の変換を行うツール群です。',
            ],
            'generator' => [
                'name' => '生成',
                'icon' => 'fa-magic',
                'description' => 'QRコード、画像など、様々なものを生成するツール群です。',
            ],
            'utility' => [
                'name' => 'ユーティリティ',
                'icon' => 'fa-tools',
                'description' => '便利な機能を提供する汎用ツール群です。',
            ],
        ];

        require_once __DIR__ . '/../Views/tools/index.php';
    }

    public function show(int $id): void
    {
        $tool = $this->toolService->getToolById($id);
        if (!$tool) {
            header('HTTP/1.0 404 Not Found');
            echo 'Tool not found';
            return;
        }

        require_once __DIR__ . '/../Views/tools/show.php';
    }
} 
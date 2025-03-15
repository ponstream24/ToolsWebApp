<?php

namespace Infrastructure\Persistence;

use Domain\Entities\Tool;
use Domain\Repositories\IToolRepository;

class JsonToolRepository implements IToolRepository
{
    private string $jsonFile;

    public function __construct(string $jsonFile = 'tools.json')
    {
        $this->jsonFile = $jsonFile;
    }

    /**
     * @return Tool[]
     */
    public function findAll(): array
    {
        $data = $this->readJson();
        return array_map(fn ($item) => $this->createToolFromArray($item), $data);
    }

    public function findById(int $id): ?Tool
    {
        $tools = $this->findAll();
        foreach ($tools as $tool) {
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
            $this->findAll(),
            fn (Tool $tool) => $tool->getCategory() === $category
        );
    }

    public function save(Tool $tool): void
    {
        $tools = $this->findAll();
        $found = false;

        foreach ($tools as $key => $existingTool) {
            if ($existingTool->getId() === $tool->getId()) {
                $tools[$key] = $tool;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $tools[] = $tool;
        }

        $this->writeJson($tools);
    }

    public function delete(int $id): void
    {
        $tools = $this->findAll();
        $tools = array_filter(
            $tools,
            fn (Tool $tool) => $tool->getId() !== $id
        );
        $this->writeJson($tools);
    }

    private function readJson(): array
    {
        if (!file_exists($this->jsonFile)) {
            return [];
        }
        $content = file_get_contents($this->jsonFile);
        return json_decode($content, true) ?? [];
    }

    private function writeJson(array $tools): void
    {
        $data = array_map(function (Tool $tool) {
            return [
                'id' => $tool->getId(),
                'name' => $tool->getName(),
                'description' => $tool->getDescription(),
                'link' => $tool->getLink(),
                'category' => $tool->getCategory()
            ];
        }, $tools);

        file_put_contents($this->jsonFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function createToolFromArray(array $data): Tool
    {
        return new Tool(
            $data['id'] ?? 0,
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['link'] ?? '',
            $data['category'] ?? 'utility'
        );
    }
} 
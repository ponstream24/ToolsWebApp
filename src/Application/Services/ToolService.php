<?php

namespace Application\Services;

use Domain\Entities\Tool;
use Domain\Repositories\IToolRepository;

class ToolService
{
    private IToolRepository $repository;

    public function __construct(IToolRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Tool[]
     */
    public function getAllTools(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @return Tool[]
     */
    public function getToolsByCategory(string $category): array
    {
        return $this->repository->findByCategory($category);
    }

    public function getToolById(int $id): ?Tool
    {
        return $this->repository->findById($id);
    }

    public function createTool(
        string $name,
        string $description,
        string $link,
        string $category
    ): Tool {
        $tools = $this->repository->findAll();
        $maxId = 0;
        foreach ($tools as $tool) {
            $maxId = max($maxId, $tool->getId());
        }

        $tool = new Tool($maxId + 1, $name, $description, $link, $category);
        $this->repository->save($tool);
        return $tool;
    }

    public function updateTool(
        int $id,
        string $name,
        string $description,
        string $link,
        string $category
    ): ?Tool {
        $existingTool = $this->repository->findById($id);
        if (!$existingTool) {
            return null;
        }

        $tool = new Tool($id, $name, $description, $link, $category);
        $this->repository->save($tool);
        return $tool;
    }

    public function deleteTool(int $id): bool
    {
        $existingTool = $this->repository->findById($id);
        if (!$existingTool) {
            return false;
        }

        $this->repository->delete($id);
        return true;
    }
} 
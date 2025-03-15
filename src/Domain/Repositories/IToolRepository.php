<?php

namespace Domain\Repositories;

use Domain\Entities\Tool;

interface IToolRepository
{
    /**
     * @return Tool[]
     */
    public function findAll(): array;

    public function findById(int $id): ?Tool;

    /**
     * @return Tool[]
     */
    public function findByCategory(string $category): array;

    public function save(Tool $tool): void;

    public function delete(int $id): void;
} 
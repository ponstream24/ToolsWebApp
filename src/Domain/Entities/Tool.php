<?php

namespace Domain\Entities;

class Tool
{
    private int $id;
    private string $name;
    private string $description;
    private string $link;
    private string $category;

    public function __construct(
        int $id,
        string $name,
        string $description,
        string $link,
        string $category
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->link = $link;
        $this->category = $category;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getCategory(): string
    {
        return $this->category;
    }
} 
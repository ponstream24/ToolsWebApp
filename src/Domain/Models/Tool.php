<?php

namespace Domain\Models;

class Tool
{
    private string $name;
    private string $description;
    private string $link;
    private string $category;

    public function __construct(
        string $name,
        string $description,
        string $link,
        string $category
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->link = $link;
        $this->category = $category;
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

    public function getUrl(): string
    {
        return $this->link;
    }
} 
<?php
declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

class LevelNotFoundException extends RuntimeException
{
    private ?string $username;

    public function __construct(private int $level)
    {
        parent::__construct();
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }
}

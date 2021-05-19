<?php declare(strict_types=1);

namespace App\Exception;

use Throwable;

class LevelNotFoundException extends \RuntimeException
{
    public function __construct(private int $level)
    {
        parent::__construct();
    }

    public function getLevel(): int
    {
        return $this->level;
    }
}
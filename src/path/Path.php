<?php

namespace Supermetrolog\SynchronizerFilesystemSourceRepo\path;

use LogicException;

abstract class Path
{
    protected string $path;
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->normalize();
    }
    public function getPath(): string
    {
        return $this->path;
    }
    public function __toString(): string
    {
        return $this->path;
    }

    protected function normalize(): void
    {
        $path = $this->path;
        $path = trim($path);
        $path = preg_replace('!\\\+!', "/", $path);
        if (!is_string($path)) {
            throw new LogicException("preg_replace return not string value");
        }
        $this->path = $path;
    }

    protected function isEmpty(): bool
    {
        return $this->len() === 0;
    }

    protected function lastSymbolIsSlash(): bool
    {
        return $this->getSymbol($this->len() - 1) == "/";
    }

    protected function getSymbol(int $index): string
    {
        return mb_substr($this->path, $index, 1);
    }
    protected function len(): int
    {
        return mb_strlen($this->path);
    }
}

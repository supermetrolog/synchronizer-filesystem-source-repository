<?php

namespace Supermetrolog\SynchronizerFilesystemSourceRepo\path;

use InvalidArgumentException;
use LogicException;

class AbsPath extends Path
{
    public function __construct(string $path)
    {
        parent::__construct($path);
        $this->normalizePath();
    }
    private function normalizePath(): void
    {
        if ($this->isEmpty()) {
            throw new InvalidArgumentException("asbsolute path cannot be empty");
        }
        if ($this->lastSymbolIsSlash()) {
            $this->removeLastSlash();
        }
    }

    public function addRelativePath(string $relPath): self
    {
        $path = $this . new RelPath($relPath);
        return new self($path);
    }
    private function removeLastSlash(): void
    {
        $this->path = mb_substr($this->path, 0, -1);
    }
}

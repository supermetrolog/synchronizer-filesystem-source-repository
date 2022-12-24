<?php

namespace Supermetrolog\SynchronizerFilesystemSourceRepo\path;

class RelPath extends Path
{
    public function __construct(string $path = "")
    {
        parent::__construct($path);
        $this->normalizePath();
    }

    private function normalizePath(): void
    {
        if ($this->isEmpty()) {
            $this->path = "/";
            return;
        }

        if (!$this->firstSymbolIsSlash()) {
            $this->addFirstSlash();
        }

        if ($this->len() !== 1 && !$this->lastSymbolIsSlash()) {
            $this->addLastSlash();
        }
    }
    private function firstSymbolIsSlash(): bool
    {
        return $this->getSymbol(0) == "/";
    }
    private function addFirstSlash(): void
    {
        $this->path = "/$this->path";
    }

    private function addLastSlash(): void
    {
        $this->path = $this->path . "/";
    }
}

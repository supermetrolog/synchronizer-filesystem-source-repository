<?php

namespace Supermetrolog\SynchronizerFilesystemSourceRepo\path;

abstract class Path
{
    protected string $path;
    public function __construct(string $path)
    {
        $this->path = $path;
    }
    public function getPath(): string
    {
        return $this->path;
    }
    public function __toString(): string
    {
        return $this->path;
    }
}

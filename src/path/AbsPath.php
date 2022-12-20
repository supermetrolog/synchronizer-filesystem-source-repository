<?php

namespace Supermetrolog\SynchronizerFilesystemSourceRepo\path;

use InvalidArgumentException;
use LogicException;

class AbsPath extends Path
{
    public function __construct(string $path)
    {
        $path = preg_replace('!\\\+!', "/", $path);
        if (!is_string($path)) {
            throw new LogicException("preg_replace return not string value");
        }
        if (mb_strlen($path) === 0) {
            throw new InvalidArgumentException("asbsolute path cannot be empty");
        }
        if ($path[mb_strlen($path) - 1] == "/") {
            $path = substr($path, 0, -1);
        }

        $this->path = $path;
    }

    public function addRelativePath(string $relPath): self
    {
        $path = $this . new RelPath($relPath);
        return new self($path);
    }
}

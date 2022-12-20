<?php

namespace Supermetrolog\SynchronizerFilesystemSourceRepo\path;

use LogicException;

class RelPath extends Path
{
    public function __construct(string $path = "")
    {
        $path = preg_replace('!\\\+!', "/", $path);
        if (!is_string($path)) {
            throw new LogicException("preg_replace return not string value");
        }
        $path = preg_replace('!/+!', "/", $path);
        if (!is_string($path)) {
            throw new LogicException("preg_replace return not string value");
        }
        if (mb_strlen($path) === 0) {
            $this->path = "/";
            return;
        }
        if ($path[0] !== "/") {
            $path = "/$path";
        }
        if (mb_strlen($path) !== 1 && $path[mb_strlen($path) - 1] !== "/") {
            $path = "$path/";
        }
        $this->path = $path;
    }
}

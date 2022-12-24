<?php

declare(strict_types=1);

namespace tests\unit\path;

use PHPUnit\Framework\TestCase;
use Supermetrolog\SynchronizerFilesystemSourceRepo\path\RelPath;

class RelPathTest extends TestCase
{
    public function testSingleWordPath(): void
    {
        $this->assertEquals("/folder/", new RelPath("folder"));
    }

    public function testPathWithBackslashes(): void
    {
        $path = "folder\\folder2\\user/home\\";
        $this->assertEquals("/folder/folder2/user/home/", new RelPath($path));
    }

    public function testWithoutSlashAtTheEndAndStart(): void
    {
        $path = "folder/folder2";
        $this->assertEquals("/folder/folder2/", new RelPath($path));
    }

    public function testEmptyPath(): void
    {
        $path = "";
        $this->assertEquals("/", new RelPath($path));
    }

    public function testSpacePath(): void
    {
        $path = " ";
        $this->assertEquals("/", new RelPath($path));
    }

    public function testManySpacesPath(): void
    {
        $path = "      ";
        $this->assertEquals("/", new RelPath($path));
    }
}

<?php

declare(strict_types=1);

namespace tests\unit\path;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Supermetrolog\SynchronizerFilesystemSourceRepo\path\AbsPath;

class AbsPathTest extends TestCase
{
    public function testBackslashPath(): void
    {
        $path = "C:\\user\\folder\\test\\";
        $this->assertEquals("C:/user/folder/test", new AbsPath($path));
    }
    public function testSlashAtTheEnd(): void
    {
        $path = "C:/user/folder/test/";
        $this->assertEquals("C:/user/folder/test", new AbsPath($path));
    }
    public function testDotPath(): void
    {
        $path = ".";
        $this->assertEquals(".", new AbsPath($path));
    }
    public function testDotPathWithSlash(): void
    {
        $path = "/./";
        $this->assertEquals("/.", new AbsPath($path));
    }
    public function testEmptyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AbsPath("");
    }
    public function testSpacePath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AbsPath(" ");
    }
    public function testManySpacesPath(): void
    {
        $path = "      ";
        $this->expectException(InvalidArgumentException::class);
        $this->assertEquals("", new AbsPath($path));
    }
    public function testAddRelativePathWithSlashAtTheEnd(): void
    {
        $path = ".";
        $abs = new AbsPath($path);
        $this->assertEquals("./relpath/folder", $abs->addRelativePath("relpath/folder/"));
    }
    public function testAddRelativePathWithotEndSlash(): void
    {
        $path = ".";
        $abs = new AbsPath($path);
        $this->assertEquals("./relpath/folder", $abs->addRelativePath("relpath/folder"));
    }

    public function testAddRelativePathWithLongPath(): void
    {
        $path = "./absfolder/home/user/";
        $abs = new AbsPath($path);
        $this->assertEquals("./absfolder/home/user/relpath/folder", $abs->addRelativePath("relpath/folder"));
    }

    public function testWithRuPath(): void
    {
        $path = "/Users/Рабочий стол/test.txt/";
        $this->assertEquals("/Users/Рабочий стол/test.txt", new AbsPath($path));
    }
}

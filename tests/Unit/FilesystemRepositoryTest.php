<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\interfaces\FileInterface;
use Supermetrolog\Synchronizer\interfaces\StreamInterface;
use Supermetrolog\SynchronizerFilesystemSourceRepo\Filesystem;
use Supermetrolog\SynchronizerFilesystemSourceRepo\FilesystemRepository;
use Supermetrolog\SynchronizerFilesystemSourceRepo\path\AbsPath;

class FilesystemRepositoryTest extends TestCase
{
    private Filesystem $filesystem;

    public function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
    }
    public function testGetContent(): void
    {
        $baseDirectoryPath = new AbsPath(".");
        $uniqueFileName = "/fuck/suck.txt";
        $filename = $baseDirectoryPath . $uniqueFileName;
        $fileContent = "fuck the police";

        /** @var \PHPUnit\Framework\MockObject\MockObject $filesystem */
        $filesystem = $this->filesystem;
        $filesystem->expects($this->once())->method("isDir")->with($filename)->willReturn(false);
        $filesystem->expects($this->once())->method("fileExists")->with($filename)->willReturn(true);
        $filesystem->expects($this->once())->method("getContent")->with($filename)->willReturn($fileContent);

        $repo = new FilesystemRepository($baseDirectoryPath, $this->filesystem);
        /** @var \PHPUnit\Framework\MockObject\MockObject $file */
        $file = $this->createMock(FileInterface::class);
        $file->method("getUniqueName")->willReturn($uniqueFileName);
        /** @var FileInterface $file */
        $content = $repo->getContent($file);
        $this->assertEquals($fileContent, $content);
    }

    public function testGetContentWithDirFile(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $filesystem */
        $filesystem = $this->filesystem;
        $filesystem->expects($this->once())->method("isDir")->willReturn(true);
        $filesystem->expects($this->once())->method("fileExists")->willReturn(true);

        $repo = new FilesystemRepository(new AbsPath("."), $this->filesystem);
        $file = $this->createMock(FileInterface::class);

        $content = $repo->getContent($file);
        $this->assertNull($content);
    }

    public function testGetContentWithNotExistedFile(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $filesystem */
        $filesystem = $this->filesystem;
        $filesystem->expects($this->once())->method("fileExists")->willReturn(false);

        $repo = new FilesystemRepository(new AbsPath("."), $this->filesystem);
        $file = $this->createMock(FileInterface::class);

        $content = $repo->getContent($file);
        $this->assertNull($content);
    }

    public function testGetStream(): void
    {
        $repo = new FilesystemRepository(new AbsPath("."), $this->filesystem);
        $stream = $repo->getStream();
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }
}

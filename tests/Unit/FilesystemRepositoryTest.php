<?php

namespace tests\unit;

use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\interfaces\FileInterface;
use Supermetrolog\Synchronizer\interfaces\StreamInterface;
use Supermetrolog\SynchronizerFilesystemSourceRepo\Filesystem;
use Supermetrolog\SynchronizerFilesystemSourceRepo\FilesystemRepository;
use Supermetrolog\SynchronizerFilesystemSourceRepo\path\AbsPath;

class FilesystemRepositoryTest extends TestCase
{
    private Filesystem $filesystem;
    private vfsStreamDirectory $root;
    public function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->generateFiles();
    }
    private function generateFiles(): void
    {

        $this->root = vfsStream::setup();
        $this->root->addChild(vfsStream::newFile("file_suka.txt")->setContent("suka suka"));
    }

    public function testConstructor(): void
    {
        /** @var MockObject $filesystem */
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())->method("fileExists")->willReturn(true);
        $filesystem->expects($this->once())->method("isDir")->willReturn(true);
        /** @var Filesystem $filesystem */
        new FilesystemRepository(new AbsPath($this->root->url()), $filesystem);
    }
    public function testConstructorWithNotDirParam(): void
    {
        /** @var MockObject $filesystem */
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())->method("fileExists")->willReturn(true);
        $filesystem->expects($this->once())->method("isDir")->willReturn(false);
        $this->expectException(InvalidArgumentException::class);
        /** @var Filesystem $filesystem */
        new FilesystemRepository(new AbsPath($this->root->url()), $filesystem);
    }
    public function testConstructorWithNotExistParam(): void
    {
        /** @var MockObject $filesystem */
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())->method("fileExists")->willReturn(false);
        $this->expectException(InvalidArgumentException::class);
        /** @var Filesystem $filesystem */
        new FilesystemRepository(new AbsPath($this->root->url()), $filesystem);
    }
    public function testGetContent(): void
    {
        $baseDirectoryPath = new AbsPath($this->root->url());
        $uniqueFileName = "/file_suka.txt";
        $fileContent = "suka suka";

        $repo = new FilesystemRepository($baseDirectoryPath, $this->filesystem);

        /** @var MockObject $file */
        $file = $this->createMock(FileInterface::class);
        $file->method("getUniqueName")->willReturn($uniqueFileName);

        /** @var FileInterface $file */
        $content = $repo->getContent($file);

        $this->assertEquals($fileContent, $content);
    }

    public function testGetContentWithDirFile(): void
    {
        /** @var MockObject $filesystem */
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())->method("getContent")->willReturn(null);
        $filesystem->expects($this->once())->method("fileExists")->willReturn(true);
        $filesystem->expects($this->once())->method("isDir")->willReturn(true);

        /** @var Filesystem $filesystem */
        $repo = new FilesystemRepository(new AbsPath("."), $filesystem);
        $file = $this->createMock(FileInterface::class);

        $content = $repo->getContent($file);
        $this->assertNull($content);
    }

    public function testGetContentWithNotExistedFile(): void
    {
        /** @var MockObject $filesystem */
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())->method("getContent")->willReturn(null);
        $filesystem->expects($this->once())->method("fileExists")->willReturn(true);
        $filesystem->expects($this->once())->method("isDir")->willReturn(true);

        /** @var Filesystem $filesystem */
        $repo = new FilesystemRepository(new AbsPath("."), $filesystem);
        $file = $this->createMock(FileInterface::class);

        $content = $repo->getContent($file);
        $this->assertNull($content);
    }

    public function testGetStream(): void
    {
        /** @var MockObject $filesystem */
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())->method("fileExists")->willReturn(true);
        $filesystem->expects($this->once())->method("isDir")->willReturn(true);

        /** @var Filesystem $filesystem */
        $repo = new FilesystemRepository(new AbsPath("."), $filesystem);
        $stream = $repo->getStream();
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }
}

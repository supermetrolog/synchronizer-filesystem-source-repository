<?php

namespace tests\unit;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Supermetrolog\SynchronizerFilesystemSourceRepo\Filesystem;
use Supermetrolog\SynchronizerFilesystemSourceRepo\path\AbsPath;
use Supermetrolog\SynchronizerFilesystemSourceRepo\Stream;
use Supermetrolog\Synchronizer\interfaces\FileInterface;

class StreamTest extends TestCase
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
        $this->root = vfsStream::setup('root');
        $dir1 = vfsStream::newDirectory("dir1");
        $dir1->addChild(
            vfsStream::newFile("file1.txt")
                ->setContent("fuck the police")
        );
        $dir1->addChild(vfsStream::newFile("file2.txt")->setContent("fuck the police suka"));
        $dir1_1 = vfsStream::newDirectory("dir1_1");
        $dir1_1->addChild(vfsStream::newFile("file1_1.jpg")->setContent("fuck the police jpg"));
        $dir1_2 = vfsStream::newDirectory("dir1_2");

        $dir1->addChild($dir1_1);
        $dir1->addChild($dir1_2);

        $this->root->addChild($dir1);
        $this->root->addChild($dir1_2);
        $this->root->addChild(vfsStream::newFile("file_suka.txt")->setContent("suka suka"));
    }
    public function testRead(): void
    {
        $stream = new Stream(new AbsPath($this->root->url()), $this->filesystem);
        /** @var FileInterface[] $files */
        $files = [];
        foreach ($stream->read() as $file) {
            $files[] = $file;
        }
        $this->assertCount(8, $files);

        $this->assertEquals("/dir1/file1.txt", $files[0]->getUniqueName());
        $this->assertFalse($files[0]->isDir());
        $this->assertEquals(hash_file("md5", $this->root->getChild('dir1/file1.txt')->url()), $files[0]->getHash());

        $this->assertEquals("/dir1/file2.txt", $files[1]->getUniqueName());
        $this->assertFalse($files[1]->isDir());
        $this->assertEquals(hash_file("md5", $this->root->getChild('dir1/file2.txt')->url()), $files[1]->getHash());

        $this->assertEquals("/dir1/dir1_1/file1_1.jpg", $files[2]->getUniqueName());
        $this->assertFalse($files[2]->isDir());
        $this->assertEquals(
            hash_file("md5", $this->root->getChild('dir1/dir1_1/file1_1.jpg')->url()),
            $files[2]->getHash()
        );

        $this->assertEquals("/dir1/dir1_1", $files[3]->getUniqueName());
        $this->assertTrue($files[3]->isDir());

        $this->assertEquals("/dir1/dir1_2", $files[4]->getUniqueName());
        $this->assertTrue($files[4]->isDir());

        $this->assertEquals("/dir1", $files[5]->getUniqueName());
        $this->assertTrue($files[5]->isDir());

        $this->assertEquals("/dir1_2", $files[6]->getUniqueName());
        $this->assertTrue($files[6]->isDir());

        $this->assertEquals("/file_suka.txt", $files[7]->getUniqueName());
        $this->assertFalse($files[7]->isDir());
        $this->assertEquals(
            hash_file("md5", $this->root->getChild('file_suka.txt')->url()),
            $files[7]->getHash()
        );
    }

    public function testRedWithIsNotReadableFile(): void
    {

        $this->root->addChild(vfsStream::newFile(
            "file_not_readable.sock",
            0000
        ));

        $stream = new Stream(new AbsPath($this->root->url()), $this->filesystem);

        /** @var FileInterface[] $files */
        $files = [];
        foreach ($stream->read() as $file) {
            $files[] = $file;
        }

        $stream = new Stream(new AbsPath($this->root->url()), $this->filesystem);


        /** @param FileInterface $elem */
        $notReadableFile = array_search(
            "/file_not_readable.sock",
            array_map(
                function ($elem) {
                    return $elem->getUniqueName();
                },
                $files
            )
        );

        $this->assertFalse($notReadableFile);
    }
}

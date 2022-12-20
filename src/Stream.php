<?php

namespace Supermetrolog\SynchronizerFilesystemSourceRepo;

use Generator;
use LogicException;
use Supermetrolog\Synchronizer\interfaces\StreamInterface;
use Supermetrolog\SynchronizerFilesystemSourceRepo\path\AbsPath;
use Supermetrolog\SynchronizerFilesystemSourceRepo\path\RelPath;

class Stream implements StreamInterface
{
    private AbsPath $dirpath;
    private Filesystem $filesystem;

    /** @var resource $lastHandle */
    private $lastHandle;

    public function __construct(AbsPath $dirpath, Filesystem $filesystem)
    {
        $this->dirpath = $dirpath;
        $this->filesystem = $filesystem;
    }
    /**
     * @return Generator<File>
     */
    public function read(): Generator
    {
        yield from $this->readRecursive($this->dirpath);
    }
    private function readRecursive(AbsPath $dirpath, ?File $parent = null): Generator
    {
        $handle = $this->filesystem->openDir($dirpath);
        $this->lastHandle = &$handle;
        while ($filename = $this->filesystem->readDir($handle)) {
            $file = $this->createFile($filename, $dirpath, $parent);
            if (
                $file->isDir() &&
                !$file->isCurrentDirPointer() &&
                !$file->isPreventDirPointer()
            ) {
                yield from $this->readRecursive($this->getNextPath($file), $file);
            }
            if ($file->isCurrentDirPointer() || $file->isPreventDirPointer()) {
                continue;
            }
            yield $file;
        }
        $this->filesystem->closeDir($handle);
    }
    private function getFileHash(RelPath $relPath, string $filename): string
    {
        $fullpath = $this->dirpath . $relPath . $filename;
        if ($this->filesystem->isDir($fullpath)) {
            throw new LogicException("hash for directory not exist");
        }
        return $this->filesystem->hashFile(Filesystem::HASH_ALGO_MD5, $fullpath);
    }
    private function createFile(string $filename, AbsPath $dirpath, ?File $parent): File
    {
        $relPath = str_replace($this->dirpath, "", $dirpath);
        if (!is_string($relPath)) {
            throw new LogicException("str_replace return not string value");
        }
        $relPath = new RelPath($relPath);
        $fullpath = $this->dirpath . $relPath . $filename;
        $isDir = $this->filesystem->isDir($fullpath);
        $hash = "";
        if (!$isDir) {
            $hash = $this->getFileHash($relPath, $filename);
        }
        return new File($filename, $hash, $relPath, $isDir, $parent);
    }
    private function getNextPath(File $file): AbsPath
    {
        $relPath = $file->isDir() ? $file->getUniqueName() : $file->getRelPath();
        return $this->dirpath->addRelativePath($relPath);
    }

    public function __destruct()
    {
        if ($this->filesystem->isResource($this->lastHandle)) {
            $this->filesystem->closeDir($this->lastHandle);
        }
    }
}

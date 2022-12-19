<?php

namespace Supermetrolog\SynchronizerFilesystemSourceRepo;

use InvalidArgumentException;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\SourceRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\StreamInterface;
use Supermetrolog\SynchronizerFilesystemSourceRepo\path\AbsPath;

class FilesystemRepository implements SourceRepositoryInterface
{
    private AbsPath $baseDirectoryPath;
    private Filesystem $filesystem;

    public function __construct(AbsPath $baseDirectoryPath, Filesystem $filesystem)
    {
        if (!$baseDirectoryPath)
            throw new InvalidArgumentException("invalid base directory path");
        if (!file_exists($baseDirectoryPath))
            throw new InvalidArgumentException("base directory with path: $baseDirectoryPath not exist");
        if (!is_dir($baseDirectoryPath))
            throw new InvalidArgumentException("base dir path is not directory");

        $this->baseDirectoryPath = $baseDirectoryPath;
        $this->filesystem = $filesystem;
    }

    public function getStream(): StreamInterface
    {
        return new Stream($this->baseDirectoryPath, $this->filesystem);
    }
    public function getContent(FileInterface $file): ?string
    {
        $filename = $this->baseDirectoryPath->addRelativePath($file->getUniqueName());

        if (!$this->filesystem->fileExists($filename)) {
            return null;
        }
        if ($this->filesystem->isDir($filename)) {
            return null;
        }
        return $this->filesystem->getContent($filename);
    }
}

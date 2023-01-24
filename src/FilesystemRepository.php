<?php

namespace Supermetrolog\SynchronizerFilesystemSourceRepo;

use InvalidArgumentException;
use LogicException;
use Supermetrolog\Synchronizer\interfaces\FileInterface;
use Supermetrolog\Synchronizer\interfaces\SourceRepositoryInterface;
use Supermetrolog\Synchronizer\interfaces\StreamInterface;
use Supermetrolog\SynchronizerFilesystemSourceRepo\path\AbsPath;

class FilesystemRepository implements SourceRepositoryInterface
{
    private AbsPath $baseDirectoryPath;
    private Filesystem $filesystem;

    /** @var string[] $except */
    private array $except;

    /**
     *  @param string[] $except
     */
    public function __construct(AbsPath $baseDirectoryPath, Filesystem $filesystem, array $except = [])
    {
        $this->filesystem = $filesystem;
        if (!$this->filesystem->fileExists($baseDirectoryPath)) {
            throw new InvalidArgumentException("base directory with path: $baseDirectoryPath not exist");
        }
        if (!$this->filesystem->isDir($baseDirectoryPath)) {
            throw new InvalidArgumentException("base dir path is not directory");
        }

        $this->baseDirectoryPath = $baseDirectoryPath;
        $this->except = $except;
    }

    public function getStream(): StreamInterface
    {
        return new Stream($this->baseDirectoryPath, $this->filesystem, $this->except);
    }
    public function getContent(FileInterface $file): ?string
    {
        $filename = $this->baseDirectoryPath->addRelativePath($file->getUniqueName());
        if ($file->isDir()) {
            return null;
        }
        if (!$this->filesystem->fileExists($filename)) {
            throw new LogicException("file not found");
        }
        return $this->filesystem->getContent($filename);
    }
}

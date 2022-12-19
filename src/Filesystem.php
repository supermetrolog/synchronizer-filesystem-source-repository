<?php

namespace Supermetrolog\SynchronizerFilesystemSourceRepo;

use Exception;
use InvalidArgumentException;

class Filesystem
{
    public const HASH_ALGO_MD5 = "md5";

    public function fileExists(string $filename): bool
    {
        return file_exists($filename);
    }

    public function isDir(string $filename): bool
    {
        return is_dir($filename);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getContent(string $filename): ?string
    {
        if ($this->isDir($filename)) {
            throw new InvalidArgumentException("File is directory. Directory not have content");
        }

        if (!$this->fileExists($filename)) {
            throw new InvalidArgumentException("File not found, filename: \"$filename\" ");
        }

        $content = file_get_contents($filename);
        if ($content === false) {
            throw new Exception("Unknown error. Content not found");
        }
        return $content;
    }

    /** @return resource */
    public function openDir(string $directory)
    {
        $result = opendir($directory);
        if ($result === false) {
            throw new InvalidArgumentException("Open directory error");
        }
        return $result;
    }

    /**
     * @return string|false
     * @param resource $handle
     */
    public function readDir($handle)
    {
        return readdir($handle);
    }
    /** @param resource $handle */
    public function closeDir($handle): void
    {
        closedir($handle);
    }
    public function hashFile(string $algo, $filename): string
    {
        if ($this->isDir($filename)) {
            throw new InvalidArgumentException("File is directory. Directory not have hash");
        }
        $result = hash_file($algo, $filename);
        if ($result === false) {
            throw new InvalidArgumentException("Hashing file error");
        }
        return $result;
    }
    /** @param resource $handle */
    public function isResource($handle): bool
    {
        return is_resource($handle);
    }
}

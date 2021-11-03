<?php

namespace yzh52521\Flysystem\Upyun;

use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Upyun\Upyun;
use League\Flysystem\Config;

/**
 * Class UpyunAdapter
 * @package yzh52521\flysystem\Upyun
 */
class UpyunAdapter implements FilesystemAdapter
{
    /**
     * @var
     */
    protected $bucket;
    /**
     * @var
     */
    protected $operator;
    /**
     * @var
     */
    protected $password;

    /**
     * @var
     */
    protected $domain;

    /**
     * @var
     */
    protected $protocol;

    /**
     * UpyunAdapter constructor.
     * @param $bucket
     * @param $operator
     * @param $password
     * @param mixed $domain
     * @param mixed $protocol
     */
    public function __construct($bucket, $operator, $password, $domain, $protocol = 'http')
    {
        $this->bucket   = $bucket;
        $this->operator = $operator;
        $this->password = $password;
        $this->domain   = $domain;
        $this->protocol = $protocol;
    }

    /**
     * @throws \Exception
     */
    public function fileExists(string $path): bool
    {
        return $this->has($path);
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @throws \Exception
     */
    public function write(string $path, string $contents, Config $config): void
    {
        $this->client()->write($path, $contents);
    }

    /**
     * @param string $path
     * @param resource $contents
     * @param Config $config
     * @throws \Exception
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, $contents, $config);
    }

    /**
     * @param string $path
     * @param resource $contents
     * @param Config $config
     * @throws \Exception
     */
    public function update(string $path, $contents, Config $config): void
    {
        $this->write($path, $contents, $config);
    }


    /**
     * @param string $path
     * @param string $newpath
     * @param Config $config
     * @throws \Exception
     */
    public function copy(string $source, string $destination, Config $config): void
    {
        $this->writeStream($destination, fopen($this->getUrl($source), 'rb'), $config);
    }

    /**
     * @param string $path
     * @throws \Exception
     */
    public function delete(string $path): void
    {
        $this->client()->delete($path);
    }

    /**
     * @param string $dirname
     * @throws \Exception
     */
    public function deleteDirectory(string $path): void
    {
        $this->client()->deleteDir($path);
    }

    /**
     * @param string $dirname
     * @param Config $config
     * @throws \Exception
     */
    public function createDirectory($dirname, Config $config): void
    {
        $this->client()->createDir($dirname);
    }

    /**
     * @param string $path
     * @param string $visibility
     */
    public function setVisibility($path, $visibility): void
    {
    }

    /**
     * @param string $path
     * @throws \Exception
     */
    public function has(string $path): bool
    {
        return $this->client()->has($path);
    }

    /**
     * @param string $path
     * @return string;
     */
    public function read(string $path): string
    {
        $result = file_get_contents($this->getUrl($path));
        if ($result === false) {
            throw UnableToReadFile::fromLocation($path);
        }
        return $result;
    }

    /**
     * @param string $path
     * @param resource
     */
    public function readStream(string $path): bool
    {
        if ($result = fopen($this->getUrl($path), 'rb')) {
            return $result;
        }
        throw UnableToReadFile::fromLocation($path);
    }

    /**
     * @param string $path
     * @param bool $deep
     * @return iterable
     * @throws \Exception
     */
    public function listContents(string $path, bool $deep): iterable
    {
        $list = [];

        $result = $this->client()->read($path, null, ['X-List-Limit' => 100, 'X-List-Iter' => null]);

        foreach ($result['files'] as $files) {
            $list[] = $this->normalizeFileInfo($files, $path);
        }

        return $list;
    }

    /**
     * @param string $path
     * @return array
     */
    public function getMetadata(string $path): array
    {
        return $this->client()->info($path);
    }

    /**
     * @param string $path
     * @return array
     */
    public function getType(string $path): array
    {
        $response = $this->getMetadata($path);

        return ['type' => $response['x-upyun-file-type']];
    }

    /**
     * @param string $path
     */
    public function getSize(string $path): array
    {
        $response = $this->getMetadata($path);

        return ['size' => $response['x-upyun-file-size']];
    }

    public function fileSize(string $path): FileAttributes
    {
        $size = $this->getSize($path);
        return new FileAttributes($path, null, null, null, $size['size']);
    }

    /**
     * @param string $path
     */
    public function mimeType(string $path): FileAttributes
    {
        $response = $this->getMetadata($path);
        return new FileAttributes($path, null, null, null, $response['content-type']);
    }


    /**
     * @param string $path
     * @return FileAttributes
     */
    public function lastModified(string $path): FileAttributes
    {
        $response = $this->getMetadata($path);
        return new FileAttributes($path, null, null, $response['last-modified']);
    }


    /**
     * @param $path
     * @return string
     */
    public function getUrl($path): string
    {
        return $this->normalizeHost($this->domain) . $path;
    }

    /**
     * @param string $source
     * @param string $destination
     * @param Config $config
     * @throws \Exception
     */
    public function move(string $source, string $destination, Config $config): void
    {
        $this->client()->move($source, $destination);
    }


    public function visibility(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::visibility($path);
    }


    /**
     * @return Upyun
     */
    protected function client(): Upyun
    {
        $config         = new \Upyun\Config($this->bucket, $this->operator, $this->password);
        $config->useSsl = $this->protocol === 'https';
        return new Upyun($config);
    }

    /**
     * Normalize the file info.
     *
     * @param array $stats
     * @param string $directory
     *
     * @return array
     */
    protected function normalizeFileInfo(array $stats, string $directory): array
    {
        $filePath = ltrim($directory . '/' . $stats['name'], '/');

        return [
            'type'      => $this->getType($filePath)['type'],
            'path'      => $filePath,
            'timestamp' => $stats['time'],
            'size'      => $stats['size'],
        ];
    }


    /**
     * @param $domain
     * @return string
     */
    protected function normalizeHost($domain): string
    {
        if (0 !== stripos($domain, 'https://') && 0 !== stripos($domain, 'http://')) {
            $domain = $this->protocol . "://{$domain}";
        }

        return rtrim($domain, '/') . '/';
    }
}


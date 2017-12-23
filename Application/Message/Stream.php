<?php
declare(strict_types = 1);
namespace Application\Message;

use RuntimeException;
use InvalidArgumentException;
use Application\Message\StreamInterface;


class Stream implements StreamInterface
{
    const CHUNK_SIZE = 4096;

    /**
     * @link http://php.net/manual/en/function.fopen.php
     * @var array
     */
    const MODES = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+']
    ];

    /**
     * @var resource
     */
    protected $handle;

    /**
     * @var array
     */
    protected $metadata;

    /**
     * @var bool
     */
    protected $readable;

    /**
     * @var bool
     */
    protected $writable;

    /**
     * @var bool
     */
    protected $seekable;

    /**
     * @var int|null
     */
    protected $size;

    public function __construct($handle)
    {
        if (is_resource($handle) === false) {
            throw new InvalidArgumentException();
        }

        $this->handle = $handle;
    }

    public function __toString(): string
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (RuntimeException $exception) {
            return '';
        }
    }

    public function close(): void
    {
        fclose($this->handle);
        $this->handle = null;
        $this->readable = null;
        $this->writable = null;
        $this->seekable = null;
        $this->size = null;
    }

    public function detach()
    {
        $handle = $this->handle;
        $this->handle = null;
        $this->readable = null;
        $this->writable = null;
        $this->seekable = null;
        $this->size = null;

        return $handle;
    }

    public function getSize(): ?int
    {
        if ($this->size === null) {
            $stats = fstat($this->handle);
            return $stats['size'] ?? null;
        }

        return $this->size;
    }

    public function tell(): int
    {
        $position = ftell($this->handle);

        if ($position === false) {
            throw new RuntimeException();
        }

        return $position;
    }

    public function eof(): bool
    {
        return feof($this->handle);
    }

    public function isSeekable(): bool
    {
        if ($this->seekable === null) {
            $this->seekable = false;
            $metadata = $this->getMetadata();
            $this->seekable = isset($metadata['seekable']);
        }

        return $this->seekable;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->isSeekable() ||
            fseek($this->handle, $offset, $whence) === -1
        ) {
            throw new RuntimeException();
        }
    }

    public function rewind(): void
    {
        if (!$this->isSeekable() || rewind($this->handle) === false) {
            throw new RuntimeException();
        }
    }

    public function isWritable(): bool
    {
        if ($this->writable === null) {
            $this->writable = false;
            $metadata = $this->getMetadata();

            foreach (self::MODES['writable'] as $mode) {
                if (strpos($metadata['mode'], $mode) === 0) {
                    $this->writable = true;
                    break;
                }
            }
        }

        return $this->writable;
    }

    public function write(string $string): int
    {
        if (!$this->isWritable() ||
            ($bytes = fwrite($this->handle, $string)) === false
        ) {
            throw new RuntimeException();
        }

        return $bytes;
    }

    public function isReadable(): bool
    {
        if ($this->readable === null) {
            $this->readable = false;
            $metadata = $this->getMetadata();

            foreach (self::MODES['writable'] as $mode) {
                if (strpos($metadata['mode'], $mode) === 0) {
                    $this->readable = true;
                    break;
                }
            }
        }

        return $this->readable;
    }

    public function read(int $length): string
    {
        if (!$this->isReadable() ||
            ($data = fread($this->handle, $length)) === false
        ) {
            throw new RuntimeException();
        }

        return $data;
    }

    public function getContents(): string
    {
        if (!$this->isReadable() ||
            ($contents = stream_get_contents($this->handle)) === false
        ) {
            throw new RuntimeException();
        }

        return $contents;
    }

    public function getMetadata(?string $key = null)
    {
        $this->metadata = stream_get_meta_data($this->handle);

        if ($key === null) {
            return $this->metadata;
        }

        return $this->metadata[$key] ?? null;
    }
}

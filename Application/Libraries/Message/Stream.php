<?php
namespace Application\Libraries\Message;

use RuntimeException;
use InvalidArgumentException;
use Application\Libraries\Message\Interfaces\StreamInterface;


class Stream implements StreamInterface
{
    const CHUNK_SIZE = 4096;

    /**
     * Modes which is used to specify the type of access to a stream.
     *
     * @link http://php.net/manual/en/function.fopen.php
     * @var array
     */
    const MODES = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+']
    ];

    /**
     * PHP resource reference to the stream.
     *
     * @var resource
     */
    protected $handle;

    /**
     * The metadata which describes the stream.
     *
     * @var array
     */
    protected $metadata;

    /**
     * Indicates whether the stream is readable.
     *
     * @var bool
     */
    protected $readable;

    /**
     * Indicates whether the stream is writable.
     *
     * @var bool
     */
    protected $writable;

    /**
     * Indicates whether the stream is seekable.
     *
     * @var bool
     */
    protected $seekable;

    /**
     * Size of the stream in bytes.
     *
     * @var int|null
     */
    protected $size;

    /**
     * @param resource $resource
     * @throws InvalidArgumentException
     */
    public function __construct($handle)
    {
        if (is_resource($handle) === false) {
            throw new InvalidArgumentException();
        }

        $this->handle = $handle;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (RuntimeException $exception) {
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        fclose($this->handle);

        $this->handle = null;
        $this->readable = null;
        $this->writable = null;
        $this->seekable = null;
        $this->size = null;
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        if ($this->size === null) {
            $stats = fstat($this->handle);
            return $stats['size'] ?? null;
        }

        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function tell(): int
    {
        $position = ftell($this->handle);

        if ($position === false) {
            throw new RuntimeException();
        }

        return $position;
    }

    /**
     * @inheritDoc
     */
    public function eof(): bool
    {
        return feof($this->handle);
    }

    /**
     * @inheritDoc
     */
    public function isSeekable(): bool
    {
        if ($this->seekable === null) {
            $this->seekable = false;
            $metadata = $this->getMetadata();

            $this->seekable = isset($metadata['seekable']);
        }

        return $this->seekable;
    }

    /**
     * @inheritDoc
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->isSeekable() ||
            fseek($this->handle, $offset, $whence) === -1
        ) {
            throw new RuntimeException();
        }
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        if (!$this->isSeekable() || rewind($this->handle) === false) {
            throw new RuntimeException();
        }
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function write(string $string): int
    {
        if (!$this->isWritable() ||
            ($bytes = fwrite($this->handle, $string)) === false
        ) {
            throw new RuntimeException();
        }

        return $bytes;
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function read(int $length): string
    {
        if (!$this->isReadable() ||
            ($data = fread($this->handle, $length)) === false
        ) {
            throw new RuntimeException();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getContents(): string
    {
        if (!$this->isReadable() ||
            ($contents = stream_get_contents($this->handle)) === false
        ) {
            throw new RuntimeException();
        }
        
        return $contents;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata(?string $key = null)
    {
        $this->metadata = stream_get_meta_data($this->handle);

        if ($key === null) {
            return $this->metadata;
        }

        return $this->metadata[$key] ?? null;
    }
}

<?php
namespace Blog\Message;

use RuntimeException;
use InvalidArgumentException;

/**
 * Represents a data stream.
 */
class Stream
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
     * Closes the stream.
     *
     * @link http://php.net/manual/en/function.fclose.php
     * @return void
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
     * Detaches the stream and returns the resource handle.
     *
     * @return resource|null Returns the resource handle or null if there's none.
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
     * Get the current size of the stream in bytes if available.
     *
     * @link http://php.net/manual/en/function.fstat.php
     * @return int|null Returns the size or null if unknown.
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
     * Get the position of the cursor in the stream.
     *
     * @link http://php.net/manual/en/function.ftell.php
     * @return int Position of the file pointer.
     * @throws RuntimeException on failure.
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
     * Return whether the cursor has reached the end of the stream.
     *
     * @link http://php.net/manual/en/function.feof.php
     * @return bool
     */
    public function eof(): bool
    {
        return feof($this->handle);
    }

    /**
     * Return whether the stream is seekable.
     *
     * @return bool
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
     * Seek the cursor to another position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Offset in bytes to the desired position.
     * @param int $whence Specifies where to offset should start.
     * @return void
     * @throws RuntimeException on failure.
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
     * Seek to the beginning of the stream and set the cursor position.
     *
     * @link http://php.net/manual/en/function.rewind.php
     * @return void
     * @throws RuntimeException on failure.
     */
    public function rewind(): void
    {
        if (!$this->isSeekable() || rewind($this->handle) === false) {
            throw new RuntimeException();
        }
    }

    /**
     * Returns whether the stream is writable.
     *
     * @return bool
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
     * Write a sequence of data to the stream. The stream mode dictates how the
     * data is written to the stream.
     *
     * @link http://php.net/manual/en/function.fwrite.php
     * @param string The sequence of data that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws RuntimeException on failure.
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
     * Returns whether the stream is readable.
     *
     * @return bool
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
     * Reads a specified amout of bytes from the current cursor position.
     *
     * @link http://php.net/manual/en/function.fread.php
     * @param int $length Amount of bytes to read.
     * @return string Returns the data which was read.
     * @throws RuntimeException on failure.
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
     * Reads and returns the remainder of data into a string.
     *
     * @link http://php.net/manual/en/function.stream-get-contents.php
     * @return string Returns the remaining data.
     * @throws RuntimeException on failure.
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
     * Reads and returns the remainder of data into a string.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @return array|mixed|null Returns an array of metadata if no key is
     *   provided, the key value if a key is provided or null if the key is not
     *   found.
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

<?php
namespace Application\Message;

class UploadedFile implements UploadedFileInterface
{
    /**
     * @var array
     */
    const ERRORS = [
        UPLOAD_ERR_OK,
        UPLOAD_ERR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE,
        UPLOAD_ERR_PARTIAL,
        UPLOAD_ERR_NO_FILE,
        UPLOAD_ERR_NO_TMP_DIR,
        UPLOAD_ERR_CANT_WRITE,
        UPLOAD_ERR_EXTENSION
    ];

    /**
     * @var StreamInterface
     */
    protected $stream;

    /**
     * @var resource
     */
    protected $file;

    /**
     * @var int|null
     */
    protected $size;

    /**
     * @var int
     */
    protected $error;

    /**
     * @var string|null
     */
    protected $clientFilename;

    /**
     * @var string|null
     */
    protected $clientMediaType;

    /**
     * @var bool
     */
    protected $moved;

    /**
     * @param resource $target
     */
    public function __construct(
        $target,
        ?int $size = null,
        int $error,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ) {
        // Notice that UPLOAD_ERR_OK is passed on success
        if (!isset(self::ERRORS[$error]) || !is_resource($target)) {
            throw new InvalidArgumentException();
        }

        $this->file = $target;
        $this->stream = new Stream(fopen($this->file, 'r'));
        $this->size = $size;
        $this->error = $error;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    public function getStream(): StreamInterface
    {
        if ($this->stream === null) {
            throw new RuntimeException();
        }
        return $this->stream;
    }

    public function moveTo(string $targetPath): void
    {
        if ($this->moved ||
            empty($targetPath) ||
            !is_writable(dirname($targetPath))
        ) {
            throw new RuntimeException();
        }

        $handle = fopen($targetPath, 'wb+');

        if ($handle === false) {
            throw new RuntimeException();
        }

        $this->stream->rewind();

        while (!$this->stream->eof()) {
            fwrite($handle, $this->stream->read(Stream::CHUNK_SIZE));
        }

        fclose($handle);
        $this->moved = true;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}

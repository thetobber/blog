<?php
declare(strict_types = 1);
namespace Application\Message;

use Application\Message\StreamInterface;

/**
 * @link https://github.com/php-fig/http-message/blob/master/src/UploadedFileInterface.php
 */
interface UploadedFileInterface
{
    public function getStream(): StreamInterface;
    public function moveTo(string $targetPath): void;
    public function getSize(): ?int;
    public function getError(): int;
    public function getClientFilename(): ?string;
    public function getClientMediaType(): ?string;
}

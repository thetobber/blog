<?php
declare(strict_types = 1);
namespace Application\Message;

use Application\Message\MessageInterface;

/**
 * @link https://github.com/php-fig/http-message/blob/master/src/ResponseInterface.php
 */
interface ResponseInterface extends MessageInterface
{
    public function getStatusCode(): int;
    public function withStatus(int $code, string $reasonPhrase = '');
    public function getReasonPhrase(): string;
}

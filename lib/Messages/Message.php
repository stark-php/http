<?php namespace Stark\Http\Messages;

use Stark\Psr\Http\Message\{MessageInterface, StreamInterface};
use OutOfBoundsException;

class Message implements MessageInterface
{
    protected $protocol_version;

    public function __construct()
    {
        $this->protocol_version = floatval(ltrim($_SERVER['SERVER_PROTOCOL'], 'HTTP/'));
    }

    public function getProtocolVersion(): string
    {
        return $this->protocol_version;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        if (floatval($version)) {
            $this->protocol_version = floatval($version);
        }
        return $this;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function hasHeader(string $name): bool
    {
        return true;
    }

    public function getHeader(string $name): array
    {
        return [];
    }

    public function getHeaderLine(string $name): string
    {
        return '';
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        return $this;
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        return $this;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        return $this;
    }

    // Should return StreamInterface
    public function getBody(): StreamInterface
    {
        return '';
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        return $this;
    }
}

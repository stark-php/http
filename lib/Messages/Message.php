<?php namespace Stark\Http\Messages;

use Stark\Psr\Http\Message\{MessageInterface, StreamInterface};
use OutOfBoundsException;

class Message implements MessageInterface
{
    protected $protocol_version;
    protected $headers = [];

    public function __construct()
    {
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            $this->protocol_version = floatval(ltrim($_SERVER['SERVER_PROTOCOL'], 'HTTP/'));
        } else {
            $this->protocol_version = '';
        }

        foreach ($this->getInitialHeaders() as $header) {
            preg_match("/^([^:]*):[ ]?(.*)/", $header, $matches);

            $this->headers[strtolower($matches[1])][] = $matches[2];
        }

    }

    protected function getInitialHeaders(): array
    {
        return headers_list();
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
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        // So we can do case insensative searching easier
        $lowerCaseName = strtolower($name);

        return isset($this->headers[$lowerCaseName]) ? true : false;
    }

    public function getHeader(string $name): array
    {
        $lowerCaseName = strtolower($name);

        return $this->headers[$lowerCaseName] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        $lowerCaseName = strtolower($name);

        if (isset($this->headers[$lowerCaseName])) {
            $headerLine = '';

            foreach ($this->headers[$lowerCaseName] as $value) {
                $headerLine .= "{$value}, ";
            }

            return rtrim($headerLine, ', ');
        }
        return '';
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        if (is_string($value)) {
            $value = [$value];
        }

        $this->headers[strtolower($name)] = $value;

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

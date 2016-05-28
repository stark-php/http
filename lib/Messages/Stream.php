<?php namespace Stark\Http\Messages;

use Stark\Psr\Http\Message\StreamInterface;
use Exception, RuntimeException;

class Stream implements StreamInterface
{
    /**
     * The result of fopen
     * @var string
     */
    protected $file;

    /**
     * Holds the metadata for the file
     * @var array
     */
    protected $metadata = [];

    public function __construct(string $filepath)
    {
        switch ($filepath) {
            case is_readable($filepath) and is_writable($filepath):
                $type = 'r+';
                break;

            case is_readable($filepath) and !is_writable($filepath):
                $type = 'r';
                break;

            default:
                $type = 'b';
                break;
        }

        $this->file = fopen($filepath, $type);

        if ($this->file) {
            $meta = stream_get_meta_data($this->file);

            // Take any meta that is stored like headers and convert it to an associative array
            foreach ($meta as $index => $singleMeta) {
                if (is_array($singleMeta)) {
                    foreach ($singleMeta as $innerMeta) {
                        preg_match("/^([^:]*): (.*)/", $innerMeta, $matches);

                        if (count($matches) > 1) {
                            $meta[$matches[1]] = $matches[2];
                        }
                    }
                    unset($meta[$index]);
                }
            }

            $this->metadata = $meta;
        }
    }

    public function __toString(): string
    {
        return stream_get_contents($this->file) ?? '';
    }

    public function close()
    {
        fclose($this->file);
    }

    public function detach()
    {
        $this->close();
    }

    public function getSize()
    {
        return $this->metadata['Content-Length'] ?? null;
    }

    public function tell(): int
    {
        $position = ftell($this->file);

        if (false === $position) {
            throw new RuntimeException("The file position could not be read");
        }

        return $position;
    }

    public function eof(): bool
    {
        return feof($this->file);
    }

    public function isSeekable(): bool
    {
        return $this->isReadable();
    }

    public function seek(int $offset, $whence = SEEK_SET)
    {
        if (!$this->isSeekable()) {
            throw new RuntimeException("You cannot seek this stream");
        }

        fseek($this->file, $offset, $whence);
    }

    public function rewind()
    {
    }

    public function isWritable(): bool
    {
        return is_writable($this->metadata['uri']);
    }

    public function write(string $string): int
    {
        return 0;
    }

    public function isReadable(): bool
    {
        return is_readable($this->metadata['uri']);
    }

    public function read(int $length): string
    {
        try {
            return fread($this->file, $length);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getContents(): string
    {
        return '';
    }

    public function getMetadata(string $key = null)
    {
    }
}

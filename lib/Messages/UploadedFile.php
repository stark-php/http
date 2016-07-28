<?php

namespace Stark\Http\Messages;

use InvalidArgumentException;
use RuntimeException;
use Stark\Psr\Http\Message\StreamInterface;
use Stark\Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    protected $file_reference;
    protected $already_moved = false;
    protected $error;
    protected $name;
    protected $tmp_name;
    protected $media_type;
    protected $size;

    public function __construct($file_reference)
    {
        if (is_array($file_reference)) {
            $this->file_reference = $file_reference;
        } elseif (isset($_FILES[$file_reference])) {
            $this->file_reference = $_FILES[$file_reference];
        } else {
            throw new RuntimeException('Unable to get that uploaded file');
        }

        if (isset($file_reference['error']) and is_array($file_reference['error'])) {
            foreach ($file_reference['error'] as $key => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $this->tmp_name   = $file_reference['tmp_name'][$key];
                    $this->name       = basename($file_reference['name'][$key]);
                    $this->media_type = $file_reference['type'][$key];
                    $this->size       = $file_reference['size'][$key];
                }

                $this->error = $error;
            }
        } elseif (isset($file_reference['error'])) {
            $error = $file_reference['error'];

            if ($error === UPLOAD_ERR_OK) {
                $this->tmp_name   = $file_reference['tmp_name'];
                $this->name       = basename($file_reference['name']);
                $this->media_type = $file_reference['type'];
                $this->size       = $file_reference['size'];
            } else {
                $this->error = $error;
            }
        } else {
            throw new RuntimeException('There was an issue reading that uploaded file');
        }
    }

    /**
     * Retrieve a stream representing the uploaded file.
     *
     * This method MUST return a StreamInterface instance, representing the
     * uploaded file. The purpose of this method is to allow utilizing native PHP
     * stream functionality to manipulate the file upload, such as
     * stream_copy_to_stream() (though the result will need to be decorated in a
     * native PHP stream wrapper to work with such functions).
     *
     * If the moveTo() method has been called previously, this method MUST raise
     * an exception.
     *
     * @throws \RuntimeException in cases when no stream is available or can be
     *                           created.
     *
     * @return StreamInterface Stream representation of the uploaded file.
     */
    public function getStream(): StreamInterface
    {
        if ($this->already_moved) {
            throw new RuntimeException('The stream is no longer available as the file has been moved');
        }

        return new Stream($this->tmp_name);
    }

    /**
     * Move the uploaded file to a new location.
     *
     * Use this method as an alternative to move_uploaded_file(). This method is
     * guaranteed to work in both SAPI and non-SAPI environments.
     * Implementations must determine which environment they are in, and use the
     * appropriate method (move_uploaded_file(), rename(), or a stream
     * operation) to perform the operation.
     *
     * $targetPath may be an absolute path, or a relative path. If it is a
     * relative path, resolution should be the same as used by PHP's rename()
     * function.
     *
     * The original file or stream MUST be removed on completion.
     *
     * If this method is called more than once, any subsequent calls MUST raise
     * an exception.
     *
     * When used in an SAPI environment where $_FILES is populated, when writing
     * files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
     * used to ensure permissions and upload status are verified correctly.
     *
     * If you wish to move to a stream, use getStream(), as SAPI operations
     * cannot guarantee writing to stream destinations.
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     *
     * @param string $targetPath Path to which to move the uploaded file.
     *
     * @throws \InvalidArgumentException if the $path specified is invalid.
     * @throws \RuntimeException         on any error during the move operation, or on
     *                                   the second or subsequent call to the method.
     */
    public function moveTo(string $target_path)
    {
        if ( ! is_dir($target_path)) {
            throw new InvalidArgumentException('The specified path is invalid. ' . $target_path);
        }

        if ($this->already_moved) {
            throw new RuntimeException('This file has already been moved and cannot be moved again');
        }

        if ($this->is_uploaded_file()) {
            $this->move_uploaded_file(rtrim($target_path, '/'));

            $this->already_moved = true;
        } else {
            throw new RuntimeException('That file was not uploaded');
        }
    }

    /**
     * Retrieve the file size.
     *
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @todo Once php7.1 is released come back and update the return type as they can then be nullable. See https://wiki.php.net/rfc/nullable_types
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Retrieve the error associated with the uploaded file.
     *
     * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
     *
     * If the file was uploaded successfully, this method MUST return
     * UPLOAD_ERR_OK.
     *
     * Implementations SHOULD return the value stored in the "error" key of
     * the file in the $_FILES array.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     *
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     */
    public function getError(): int
    {
        if (isset($this->error)) {
            return $this->error;
        }

        return UPLOAD_ERR_OK;
    }

    /**
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @todo Once php7.1 is released come back and update the return type as they can then be nullable. See https://wiki.php.net/rfc/nullable_types
     *
     * @return string|null The filename sent by the client or null if none
     *                     was provided.
     */
    public function getClientFilename()
    {
        return $this->name;
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "type" key of
     * the file in the $_FILES array.
     *
     * @todo Once php7.1 is released come back and update the return type as they can then be nullable. See https://wiki.php.net/rfc/nullable_types
     *
     * @return string|null The media type sent by the client or null if none
     *                     was provided.
     */
    public function getClientMediaType()
    {
        return $this->media_type;
    }

    protected function convertPHPSizeToBytes($s_size)
    {
        if (is_numeric($s_size)) {
            return $s_size;
        }
        $s_suffix = substr($s_size, -1);
        $i_value  = substr($s_size, 0, -1);
        switch (strtoupper($s_suffix)) {
        case 'P':
            $i_value *= 1024;
        case 'T':
            $i_value *= 1024;
        case 'G':
            $i_value *= 1024;
        case 'M':
            $i_value *= 1024;
        case 'K':
            $i_value *= 1024;
            break;
        }

        return $i_value;
    }

    protected function getMaximumFileUploadSize()
    {
        return min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));
    }

    protected function is_uploaded_file()
    {
        return is_uploaded_file($this->tmp_name);
    }

    protected function move_uploaded_file($location)
    {
        move_uploaded_file($this->tmp_name, "{$location}/{$this->name}");
    }
}

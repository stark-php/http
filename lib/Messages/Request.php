<?php

namespace Stark\Http\Messages;

use InvalidArgumentException;
use Stark\Psr\Http\Message\RequestInterface;
use Stark\Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{
    protected $uri = false;

    protected $method = '';

    /**
     * Valid method types.
     *
     * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
     *
     * @type array
     */
    protected $valid_methods = ['OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'TRACE', 'CONNECT'];

    public function __construct($uri = '', $method = '', $protocol_version = false, $headers = false, StreamInterface $body = null)
    {
        parent::__construct();

        if ($uri instanceof UriInterface) {
            $this->uri = $uri;
        } elseif ($uri) {
            $this->uri = new Uri($uri);
        }

        if ( ! empty($method)) {
            $this->checkIsAValidRequestMethod($method);
            $this->method = $method;
        } elseif (isset($_SERVER['REQUEST_METHOD'])) {
            $this->checkIsAValidRequestMethod($_SERVER['REQUEST_METHOD']);
            $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        }
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget(): string
    {
        if ($this->uri) {
            return (string) $this->uri;
        }

        return '/';
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
     *     request-target forms allowed in request messages)
     *
     * @param mixed $requestTarget
     *
     * @return self
     */
    public function withRequestTarget($requestTarget): RequestInterface
    {
        return new self($requestTarget);
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     *
     * @throws \InvalidArgumentException for invalid HTTP methods.
     *
     * @return self
     */
    public function withMethod($method): RequestInterface
    {
        $this->checkIsAValidRequestMethod($method);

        return new self($this->uri, $method);
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @return UriInterface Returns a UriInterface instance
     *                      representing the URI of the request.
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @param UriInterface $uri          New request URI to use.
     * @param bool         $preserveHost Preserve the original state of the Host header.
     *
     * @return self
     *
     * @todo Implement preserve host
     */
    public function withUri(UriInterface $uri, $preserveHost = false): RequestInterface
    {
        $new_request = new self($uri, $this->method, $this->protocol_version, $this->headers, $this->body);

        return $new_request;
    }

    /**
     * Checks that the method specified is Valid.
     *
     * @param string $method An HTTP method
     *
     * @throws \InvalidArgumentException for invalid HTTP methods
     */
    protected function checkIsAValidRequestMethod(string $method)
    {
        if ( ! array_search(strtoupper($method), $this->valid_methods, true)) {
            throw new InvalidArgumentException('An invalid HTTP method was specified, ' . $method);
        }
    }
}

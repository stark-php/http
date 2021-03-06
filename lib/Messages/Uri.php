<?php

namespace Stark\Http\Messages;

use Stark\Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * The scheme used with the uri.
     *
     * @type string
     */
    protected $scheme = '';

    /**
     * Username used to access the uri.
     *
     * @type string
     */
    protected $username = '';

    /**
     * Password used to access the uri.
     *
     * @type string
     */
    protected $password = '';

    /**
     * Hostname for the uri.
     *
     * @type string
     */
    protected $host = '';

    /**
     * Port for the uri.
     *
     * @type int|null
     */
    protected $port = null;

    /**
     * The path to access on the host of the uri.
     *
     * @type string
     */
    protected $path = '';

    /**
     * The query string to use on the uri.
     *
     * @type string
     */
    protected $query = '';

    /**
     * The fragment to use when accessing the uri.
     *
     * @type string
     */
    protected $fragment = '';

    public function __construct(...$uri)
    {
        // We assume that we have all the properties passed in individually
        if (is_array($uri) and count($uri) > 1) {
            $this->scheme   = $uri[0];
            $this->username = $uri[1];
            $this->password = $uri[2];
            $this->host     = $uri[3];
            $this->port     = $uri[4];
            $this->path     = $uri[5];
            $this->query    = $uri[6];
            $this->fragment = $uri[7];
        // If a uri is specified lets use that
        } else {
            $uri = $uri[0];

            preg_match('/^(https?):\/\//', $uri, $scheme);
            preg_match('/^[^:]*:\/\/([^:]*)[@|:]/', $uri, $username);
            preg_match('/^[^:]*:\/\/[^:]*:([^@]*)@/', $uri, $password);
            preg_match('/^[^:]*:\/\/([^@]*@)?([^:|\/|?|#]*)/', $uri, $host);
            preg_match('/^[^:]*:\/\/([^@]*@)?[^:|\/]*:?([0-9]*)?\//', $uri, $port);
            preg_match('/(^[^:]*:\/\/)?([^@]*@)?([^:|\/]*)?([:0-9]*)?(\/[^?|#]*)?/', $uri, $path);
            preg_match('/^[^:]*:\/\/([^@]*@)?[^:|\/]*([:0-9]*)?(\/[^?]*)?\?([^#|$]*)/', $uri, $query);
            preg_match('/^[^:]*:\/\/([^@]*@)?[^:|\/]*([:0-9]*)?(\/[^#]*)?\#([^?|$]*)/', $uri, $fragment);

            if (isset($scheme[1])) {
                $this->scheme = $scheme[1];
            }

            if (isset($host[2])) {
                $this->host = $host[2];
            }

            if (isset($username[1])) {
                $this->username = $username[1];
            }

            if (isset($password[1])) {
                $this->password = $password[1];
            }

            if (isset($port[2]) and ! empty($port[2])) {
                $this->port = $port[2];
            }

            if (isset($path[5])) {
                $this->path = $path[5];
            }

            if (isset($query[4])) {
                $this->query = $query[4];
            }

            if (isset($fragment[4])) {
                $this->fragment = $fragment[4];
            }
        }
    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     *
     * @return string The URI scheme.
     */
    public function getScheme() : string
    {
        return strtolower($this->scheme);
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     *
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority(): string
    {
        if ($this->getUserInfo()) {
            $user_info = $this->getUserInfo() . '@';
        } else {
            $user_info = '';
        }

        if ($this->getPort() === null) {
            return $user_info . $this->getHost();
        } else {
            return $user_info . $this->getHost() . ':' . $this->port;
        }
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo(): string
    {
        if ($this->username !== '' and $this->password !== '') {
            return $this->username . ':' . $this->password;
        }

        // By default username is an empty string
        return $this->username;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * @return string The URI host.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        if (($this->port === 80 and $this->scheme === 'http') or ($this->port === 443 and $this->scheme === 'https')) {
            return;
        } else {
            return $this->port;
        }
    }

    /**
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @return string The URI path.
     *
     * @todo Look into proper implementation of the above
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     *
     * @return string The URI query string.
     *
     * @todo Look into proper implementation of the above
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @return string The URI fragment.
     *
     * @todo Look into proper implementation of the above
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     *
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     *
     * @return self A new instance with the specified scheme.
     */
    public function withScheme(string $scheme): UriInterface
    {
        return $this->createNewInstanceWith('scheme', strtolower($scheme));
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string      $user     The user name to use for authority.
     * @param null|string $password The password associated with $user.
     *
     * @return self A new instance with the specified user information.
     */
    public function withUserInfo(string $user, $password = null): UriInterface
    {
        $new_properties = ['username'];
        $new_values     = [$user];

        if ($password !== null) {
            $new_properties[] = 'password';
            $new_values[]     = $password;
        }

        return $this->createNewInstanceWith($new_properties, $new_values);
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     *
     * @throws \InvalidArgumentException for invalid hostnames.
     *
     * @return self A new instance with the specified host.
     */
    public function withHost(string $host): UriInterface
    {
        return $this->createNewInstanceWith('host', $host);
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *                       removes the port information.
     *
     * @throws \InvalidArgumentException for invalid ports.
     *
     * @return self A new instance with the specified port.
     */
    public function withPort($port = null): UriInterface
    {
        return $this->createNewInstanceWith('port', $port);
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     *
     * @throws \InvalidArgumentException for invalid paths.
     *
     * @return self A new instance with the specified path.
     */
    public function withPath(string $path): UriInterface
    {
        return $this->createNewInstanceWith('path', $path);
    }

    /**
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     *
     * @throws \InvalidArgumentException for invalid query strings.
     *
     * @return self A new instance with the specified query string.
     */
    public function withQuery(string $query): UriInterface
    {
        return $this->createNewInstanceWith('query', $query);
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     *
     * @return self A new instance with the specified fragment.
     */
    public function withFragment(string $fragment): UriInterface
    {
        return $this->createNewInstanceWith('fragment', $fragment);
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     *
     * @return string
     */
    public function __toString(): string
    {
        $string = '';

        if ($scheme = $this->scheme) {
            $string .= $scheme . ':';
        }

        if ($authority = $this->getAuthority()) {
            $string .= '//' . $authority;
        }

        if ($path = $this->getPath()) {
            if (substr($path, 0, 1) !== '/') {
                $string .= '/';
            }

            $string .= $path;
        }

        if ($query = $this->getQuery()) {
            $string .= '?' . $query;
        }

        if ($fragment = $this->getFragment()) {
            $string .= '#' . $fragment;
        }

        return $string;
    }

    /**
     * Creates a new instance of Uri with the properties passed in.
     *
     * @param array|string $property An array of properties to change or just a single string
     * @param array|string $value    The values of the properties to change
     *
     * @return UriInterface A new instance of Uri that contains the new properties
     */
    protected function createNewInstanceWith($property, $value) : UriInterface
    {
        $string = (string) $this;

        $indexes = ['scheme', 'username', 'password', 'host', 'port', 'path', 'query', 'fragment'];
        $values  = [
            $this->scheme,
            $this->username,
            $this->password,
            $this->host,
            $this->port,
            $this->path,
            $this->query,
            $this->fragment,
        ];

        if (is_array($property)) {
            foreach ($property as $index => $single) {
                $values[array_search($single, $indexes, true)] = $value[$index];
            }
        } else {
            $values[array_search($property, $indexes, true)] = $value;
        }

        return new self(...$values);
    }
}

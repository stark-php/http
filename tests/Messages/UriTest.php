<?php

use Stark\Http\Messages\Uri;

class UriTest extends PHPUnit_Framework_TestCase
{
    public function testWeCanGetTheSchemeOfTheUri()
    {
        $uri = new Uri('https', '', '', 'google.com', 80, '/', '', '');

        $this->assertEquals('https', $uri->getScheme());
    }

    public function testWeCanGetTheAuthorityOfTheUri()
    {
        $uri_host_only                       = new Uri('http', '', '', 'google.com', 80, '/', '', '');
        $uri_host_and_port                   = new Uri('http', '', '', 'google.com', 8080, '/', '', '');
        $uri_host_port_username_and_password = new Uri('http', 'user', 'password', 'google.com', 80, '/', '', '');

        $this->assertEquals('google.com', $uri_host_only->getAuthority());
        $this->assertEquals('google.com:8080', $uri_host_and_port->getAuthority());
        $this->assertEquals('user:password@google.com', $uri_host_port_username_and_password->getAuthority());
    }

    public function testWeCanGetThePathOfTheUri()
    {
        $uri = new Uri('http', '', '', 'google.com', 80, '/', '', '');

        $this->assertEquals('/', $uri->getPath());
    }

    public function testWeCanGetTheQueryOfTheUri()
    {
        $uri = new Uri('http', '', '', 'google.com', 80, '/', 'foo=bar&baz=asdf', '');

        $this->assertEquals('foo=bar&baz=asdf', $uri->getQuery());
    }

    public function testWeCanGetTheFragmentOfTheUri()
    {
        $uri = new Uri('http', '', '', 'google.com', 80, '/', '', 'fragment');

        $this->assertEquals('fragment', $uri->getFragment());
    }

    public function testWeCanCreateANewInstanceWithADifferentScheme()
    {
        $uri = new Uri('http', '', '', 'google.com', 80, '/', '', 'fragment');

        $new_instance = $uri->withScheme('https');

        $this->assertEquals('https', $new_instance->getScheme());
    }

    public function testWeCanCreateANewInstanceWithDifferentUserInfo()
    {
        $uri = new Uri('http', '', '', 'google.com', 80, 'asdf', 'foo=bar', 'fragment');

        $new_instance_without_password = $uri->withUserInfo('user');
        $new_instance_with_password    = $uri->withUserInfo('user', 'password');

        $this->assertEquals('user', $new_instance_without_password->getUserInfo());
        $this->assertEquals('user:password', $new_instance_with_password->getUserInfo());
    }

    public function testWeCanCreateANewInstanceWithADifferentHost()
    {
        $uri = new Uri('http', '', '', 'google.com', 80, '/', '', 'fragment');

        $new_instance = $uri->withHost('twitter.com');

        $this->assertEquals('twitter.com', $new_instance->getHost());
    }

    public function testWeCanCreateANewInstanceWithADifferentPort()
    {
        $uri = new Uri('http', '', '', 'google.com', 80, '/', '', 'fragment');

        $new_instance              = $uri->withPort(8080);
        $new_instance_with_no_port = $uri->withPort(null);

        $this->assertEquals(8080, intval($new_instance->getPort()));
        $this->assertEquals(null, $new_instance_with_no_port->getPort());
    }

    public function testWeCanCreateANewInstanceWithADifferentPath()
    {
        $uri = new Uri('http', '', '', 'google.com', 80, '/', '', 'fragment');

        $new_instance = $uri->withPath('asdf/');

        $this->assertEquals('asdf/', $new_instance->getPath());
    }

    public function testWeCanCreateANewInstanceWithADifferentQuery()
    {
        $uri = new Uri('http', '', '', 'google.com', 80, '/', '', 'fragment');

        $new_instance = $uri->withQuery('foo=bar');

        $this->assertEquals('foo=bar', $new_instance->getQuery());
    }

    public function testWeCanCreateANewInstanceWithADifferentFragment()
    {
        $uri = new Uri('http', '', '', 'google.com', 80, '/', '', 'fragment');

        $new_instance = $uri->withFragment('new-fragment');

        $this->assertEquals('new-fragment', $new_instance->getFragment());
    }

    /**
     * Testing Uri combinations.
     */
    public function testWeCanCreateAUriFromAStringInItsLongestForm()
    {
        $uri = new Uri('http://foo:bar@google.com:8080/baz/asdf/?foo=bar#fragment');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('foo:bar@google.com:8080', $uri->getAuthority());
        $this->assertEquals('foo:bar', $uri->getUserInfo());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
        $this->assertEquals('/baz/asdf/', $uri->getPath());
        $this->assertEquals('foo=bar', $uri->getQuery());
        $this->assertEquals('fragment', $uri->getFragment());
    }

    public function testWeCanCreateAUriFromAStringInItsShortestForm()
    {
        $uri = new Uri('http://google.com/baz/asdf/');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('google.com', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/baz/asdf/', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testWeCanCreateAUriFromAStringWithUsernameAndPasswordAndHttps()
    {
        $uri = new Uri('https://foo:baz@google.com/baz/asdf/');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('foo:baz@google.com', $uri->getAuthority());
        $this->assertEquals('foo:baz', $uri->getUserInfo());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/baz/asdf/', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testWeCanCreateAUriFromAStringWithQueryAndHttps()
    {
        $uri = new Uri('https://google.com/baz/asdf/?foo=bar&lorem=ipsum');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('google.com', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/baz/asdf/', $uri->getPath());
        $this->assertEquals('foo=bar&lorem=ipsum', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testWeCanCreateAUriFromAStringWithFragmentAndHttps()
    {
        $uri = new Uri('https://google.com/baz/asdf/#fragment');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('google.com', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/baz/asdf/', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('fragment', $uri->getFragment());
    }

    public function testWeCanCreateAUriFromAStringWithQueryFragmentAndHttps()
    {
        $uri = new Uri('https://google.com/baz/asdf/?asdf=baz#fragment');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('google.com', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/baz/asdf/', $uri->getPath());
        $this->assertEquals('asdf=baz', $uri->getQuery());
        $this->assertEquals('fragment', $uri->getFragment());
    }

    public function testWeCanCreateAUriFromAStringQueryFragmentHttpsAndNoPath()
    {
        $uri = new Uri('https://google.com?asdf=baz#fragment');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('google.com', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('asdf=baz', $uri->getQuery());
        $this->assertEquals('fragment', $uri->getFragment());
    }

    public function testWeCanCreateAUriFromAStringQueryFragmentHttpsAndASingleSlashPath()
    {
        $uri = new Uri('https://google.com/?asdf=baz#fragment');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('google.com', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/', $uri->getPath());
        $this->assertEquals('asdf=baz', $uri->getQuery());
        $this->assertEquals('fragment', $uri->getFragment());
    }

    public function testWeCanCreateAUriFromAStringWithNoPath()
    {
        $uri = new Uri('https://google.com');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('google.com', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testWeCanCreateARelativeUrl()
    {
        $uri = new Uri('/foo/bar');

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/foo/bar', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }
}

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
        $uri_host_only = new Uri('http', '', '', 'google.com', 80, '/', '', '');
        $uri_host_and_port = new Uri('http', '', '', 'google.com', 8080, '/', '', '');
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
        $new_instance_with_password = $uri->withUserInfo('user', 'password');

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

        $new_instance = $uri->withPort(8080);
        $new_instance_with_no_port = $uri->withPort(NULL);

        $this->assertEquals(8080, $new_instance->getPort());
        $this->assertEquals('', $new_instance_with_no_port->getPort());
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
}

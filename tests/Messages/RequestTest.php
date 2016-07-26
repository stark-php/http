<?php

use Stark\Http\Messages\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testWeCanGetTheRequestTarget()
    {
        $request = new Request('http://google.com', 'GET');
        $empty_request = new Request();

        $this->assertEquals('http://google.com', $request->getRequestTarget());
        $this->assertEquals('/', $empty_request->getRequestTarget());
    }

    public function testWeCanGetTheRequestMethodFromTheServerGlobal()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request = new Request('http://google.com');

        $this->assertEquals('POST', $request->getMethod());
    }

    public function testWeCanChangeTheRequestTarget()
    {
        $request = new Request('http://google.com', 'GET');

        $new_instance = $request->withRequestTarget('http://google.com/foo');

        $this->assertEquals('http://google.com/foo', $new_instance->getRequestTarget());
    }

    public function testWeCannotPutInABadMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new Request('http://google.com', 'GEET');
    }
}

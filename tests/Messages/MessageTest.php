<?php

use Stark\Http\Messages\Message;
use phpmock\phpunit\PHPMock;

class MessageTest extends PHPUnit_Framework_TestCase
{
    use PHPMock;

    public function testWeCanGetTheProtocolVersion()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $message = new Message();

        $result = $message->getProtocolVersion();

        $this->assertEquals('1.1', $result);
    }

    public function testWeCanSetTheProtocolVersion()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $message = new Message();

        $message->withProtocolVersion('2.0');

        $result = $message->getProtocolVersion();

        $this->assertEquals('2.0', $result);
    }

    public function testWeCanGetAllHeaders()
    {
        $time = $this->getFunctionMock(__NAMESPACE__, "time");
        $time->expects($this->once())->willReturn(3);

        $this->assertEquals(3, time());
        // $message = new Message();
        //
        // $result = $message->getHeaders();

        // $this->assertEquals(['x-powered-by' => 'PHP'], $result);
    }
}

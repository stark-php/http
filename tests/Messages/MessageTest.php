<?php

use Stark\Http\Messages\Message;

class MessageTest extends PHPUnit_Framework_TestCase
{
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
}

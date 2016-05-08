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
        $messageMock = $this->getMockBuilder('Stark\Http\Messages\Message')
                             ->setMethods(array('getInitialHeaders'))
                             ->disableOriginalConstructor()
                             ->getMock();
        $messageMock->expects($this->once())
                    ->method('getInitialHeaders')
                    ->willReturn(['x-powered-by: PHP']);
        $messageMock->__construct();

        $result = $messageMock->getHeaders();

        $this->assertEquals(['x-powered-by' => 'PHP'], $result);
    }
}

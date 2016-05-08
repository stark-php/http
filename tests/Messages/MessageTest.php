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

        $this->assertEquals(['x-powered-by' => ['PHP']], $result);
    }

    public function testIfWeGetTheRightResultForHasHeader()
    {
        $messageMock = $this->getMockBuilder('Stark\Http\Messages\Message')
                             ->setMethods(array('getInitialHeaders'))
                             ->disableOriginalConstructor()
                             ->getMock();
        $messageMock->expects($this->once())
                    ->method('getInitialHeaders')
                    ->willReturn(['x-powered-by: PHP']);
        $messageMock->__construct();

        $poweredBy = $messageMock->hasHeader('x-powered-by');
        $nonExistantHeader = $messageMock->hasHeader('no-header-exists');

        $this->assertTrue($poweredBy);
        $this->assertFalse($nonExistantHeader);
    }

    public function testIfHasHeaderHandlesArgumentsCaseInsensitively()
    {
        $messageMock = $this->getMockBuilder('Stark\Http\Messages\Message')
                             ->setMethods(array('getInitialHeaders'))
                             ->disableOriginalConstructor()
                             ->getMock();
        $messageMock->expects($this->once())
                    ->method('getInitialHeaders')
                    ->willReturn(['x-powered-by: PHP']);
        $messageMock->__construct();

        $poweredBy = $messageMock->hasHeader('x-powered-by');
        $poweredByUppercase = $messageMock->hasHeader('X-POWERED-BY');

        $this->assertTrue($poweredBy);
        $this->assertTrue($poweredByUppercase);
    }

    public function testWeCanGetAHeaderByKeyAndItsValue()
    {
        $messageMock = $this->getMockBuilder('Stark\Http\Messages\Message')
                             ->setMethods(array('getInitialHeaders'))
                             ->disableOriginalConstructor()
                             ->getMock();
        $messageMock->expects($this->once())
                    ->method('getInitialHeaders')
                    ->willReturn(['x-powered-by: PHP']);
        $messageMock->__construct();

        $poweredBy = $messageMock->getHeader('x-powered-by');
        $poweredByUppercase = $messageMock->getHeader('X-POWERED-BY');
        $nonExistantHeader = $messageMock->getHeader('no-header-exists');

        $this->assertEquals(['PHP'], $poweredBy);
        $this->assertEquals(['PHP'], $poweredByUppercase);
        $this->assertEquals([], $nonExistantHeader);
    }
}
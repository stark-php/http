<?php

use Stark\Http\Messages\Message;
use Stark\Http\Messages\Stream;

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testWeCanGetTheProtocolVersion()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $message                    = new Message();

        $result = $message->getProtocolVersion();

        $this->assertSame('1.1', $result);
    }

    public function testWeCanSetTheProtocolVersion()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $message                    = new Message();

        $new_instance = $message->withProtocolVersion('2.0');

        $result = $new_instance->getProtocolVersion();

        $this->assertSame('2.0', $result);
    }

    public function testWeCanGetAllHeaders()
    {
        $messageMock = $this->createMockForMessage();

        $result = $messageMock->getHeaders();

        $this->assertSame(['x-powered-by' => ['PHP']], $result);
    }

    public function testIfWeGetTheRightResultForHasHeader()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy         = $messageMock->hasHeader('x-powered-by');
        $nonExistantHeader = $messageMock->hasHeader('no-header-exists');

        $this->assertTrue($poweredBy);
        $this->assertFalse($nonExistantHeader);
    }

    public function testIfHasHeaderHandlesArgumentsCaseInsensitively()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy          = $messageMock->hasHeader('x-powered-by');
        $poweredByUppercase = $messageMock->hasHeader('X-POWERED-BY');

        $this->assertTrue($poweredBy);
        $this->assertTrue($poweredByUppercase);
    }

    public function testWeCanGetAHeaderByKeyAndItsValue()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy          = $messageMock->getHeader('x-powered-by');
        $poweredByUppercase = $messageMock->getHeader('X-POWERED-BY');
        $nonExistantHeader  = $messageMock->getHeader('no-header-exists');

        $this->assertSame(['PHP'], $poweredBy);
        $this->assertSame(['PHP'], $poweredByUppercase);
        $this->assertSame([], $nonExistantHeader);
    }

    public function testWeCanGetHeaderValuesAsAString()
    {
        $messageMock = $this->createMockForMessage(['x-powered-by: PHP', 'x-powered-by: Stark']);

        $headerString                = $messageMock->getHeaderLine('x-powered-by');
        $headerStringUppercaseKey    = $messageMock->getHeaderLine('x-powered-by');
        $headerStringThatDoesntExist = $messageMock->getHeaderLine('no-header-exists');

        $this->assertSame('PHP, Stark', $headerString);
        $this->assertSame('PHP, Stark', $headerStringUppercaseKey);
        $this->assertSame('', $headerStringThatDoesntExist);
    }

    public function testWeCanReplaceHeaders()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy      = $messageMock->getHeader('x-powered-by');
        $new_instance   = $messageMock->withHeader('x-powered-by', 'Foobar');
        $replacedHeader = $new_instance->getHeader('x-powered-by');

        $this->assertSame(['PHP'], $poweredBy);
        $this->assertSame(['Foobar'], $replacedHeader);
    }

    public function testWeCanAppendToHeaders()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy      = $messageMock->getHeader('x-powered-by');
        $new_instance   = $messageMock->withAddedHeader('x-powered-by', 'Foobar');
        $appendedHeader = $new_instance->getHeader('x-powered-by');

        $this->assertSame(['PHP'], $poweredBy);
        $this->assertSame(['PHP', 'Foobar'], $appendedHeader);
    }

    public function testWeCanRemoveAHeader()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy     = $messageMock->getHeader('x-powered-by');
        $new_instance  = $messageMock->withoutHeader('x-powered-by');
        $removedHeader = $new_instance->getHeader('x-powered-by');

        $this->assertSame(['PHP'], $poweredBy);
        $this->assertSame([], $removedHeader);
    }

    public function testWeCanSetAMessageBody()
    {
        $messageMock = $this->createMockForMessage();
        $stream      = new Stream('LICENSE');

        $new_instance = $messageMock->withBody($stream);

        $this->assertSame($stream, $new_instance->getBody());
    }

    public function testAnExceptionIsThrownWhenThereIsNoBody()
    {
        $this->expectException(InvalidArgumentException::class);

        $messageMock = $this->createMockForMessage();

        $messageMock->getBody();
    }

    protected function createMockForMessage(array $headers = ['x-powered-by: PHP'])
    {
        $messageMock = $this->getMockBuilder('Stark\Http\Messages\Message')
                             ->setMethods(['getInitialHeaders'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $messageMock->expects($this->once())
                    ->method('getInitialHeaders')
                    ->willReturn($headers);
        $messageMock->__construct();

        return $messageMock;
    }
}

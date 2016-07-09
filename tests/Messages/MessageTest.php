<?php

use Stark\Http\Messages\{Message,Stream};

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
        $messageMock = $this->createMockForMessage();

        $result = $messageMock->getHeaders();

        $this->assertEquals(['x-powered-by' => ['PHP']], $result);
    }

    public function testIfWeGetTheRightResultForHasHeader()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy = $messageMock->hasHeader('x-powered-by');
        $nonExistantHeader = $messageMock->hasHeader('no-header-exists');

        $this->assertTrue($poweredBy);
        $this->assertFalse($nonExistantHeader);
    }

    public function testIfHasHeaderHandlesArgumentsCaseInsensitively()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy = $messageMock->hasHeader('x-powered-by');
        $poweredByUppercase = $messageMock->hasHeader('X-POWERED-BY');

        $this->assertTrue($poweredBy);
        $this->assertTrue($poweredByUppercase);
    }

    public function testWeCanGetAHeaderByKeyAndItsValue()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy = $messageMock->getHeader('x-powered-by');
        $poweredByUppercase = $messageMock->getHeader('X-POWERED-BY');
        $nonExistantHeader = $messageMock->getHeader('no-header-exists');

        $this->assertEquals(['PHP'], $poweredBy);
        $this->assertEquals(['PHP'], $poweredByUppercase);
        $this->assertEquals([], $nonExistantHeader);
    }

    public function testWeCanGetHeaderValuesAsAString()
    {
        $messageMock = $this->createMockForMessage(['x-powered-by: PHP', 'x-powered-by: Stark']);

        $headerString = $messageMock->getHeaderLine('x-powered-by');
        $headerStringUppercaseKey = $messageMock->getHeaderLine('x-powered-by');
        $headerStringThatDoesntExist = $messageMock->getHeaderLine('no-header-exists');

        $this->assertEquals('PHP, Stark', $headerString);
        $this->assertEquals('PHP, Stark', $headerStringUppercaseKey);
        $this->assertEquals('', $headerStringThatDoesntExist);
    }

    public function testWeCanReplaceHeaders()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy = $messageMock->getHeader('x-powered-by');
        $messageMock->withHeader('x-powered-by', 'Foobar');
        $replacedHeader = $messageMock->getHeader('x-powered-by');

        $this->assertEquals(['PHP'], $poweredBy);
        $this->assertEquals(['Foobar'], $replacedHeader);
    }

    public function testWeCanAppendToHeaders()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy = $messageMock->getHeader('x-powered-by');
        $messageMock->withAddedHeader('x-powered-by', 'Foobar');
        $appendedHeader = $messageMock->getHeader('x-powered-by');

        $this->assertEquals(['PHP'], $poweredBy);
        $this->assertEquals(['PHP', 'Foobar'], $appendedHeader);
    }

    public function testWeCanRemoveAHeader()
    {
        $messageMock = $this->createMockForMessage();

        $poweredBy = $messageMock->getHeader('x-powered-by');
        $messageMock->withoutHeader('x-powered-by');
        $removedHeader = $messageMock->getHeader('x-powered-by');

        $this->assertEquals(['PHP'], $poweredBy);
        $this->assertEquals([], $removedHeader);
    }

    public function testWeCanSetAMessageBody()
    {
        $messageMock = $this->createMockForMessage();
        $stream = new Stream('LICENSE');

        $messageMock->withBody($stream);
        $this->assertEquals($stream, $messageMock->getBody());
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
                             ->setMethods(array('getInitialHeaders'))
                             ->disableOriginalConstructor()
                             ->getMock();
        $messageMock->expects($this->once())
                    ->method('getInitialHeaders')
                    ->willReturn($headers);
        $messageMock->__construct();

        return $messageMock;
    }
}

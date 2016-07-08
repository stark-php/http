<?php

use Stark\Http\Messages\Stream;

class StreamTest extends PHPUnit_Framework_TestCase
{

    public function testWeCanGetTheBodyOfAStream()
    {
        $stream = new Stream('LICENSE');
        $licenseBody = file_get_contents('LICENSE');

        $streamCastToString = (string) $stream;
        $streamBody = $stream->getContents();

        $this->assertEquals($licenseBody, $streamCastToString);
        $this->assertEquals($licenseBody, $streamBody);
    }

    public function testWeCanGetThePositionOfAStream()
    {
        $stream = new Stream('LICENSE');

        $position = $stream->tell();

        $this->assertEquals(0, $position);
    }

    public function testWeCanGetTheSizeOfTheStream()
    {
        $streamWithNoLength = new Stream('LICENSE');
        $streamWithLength = new Stream('http://placehold.it/1500x1500');
        $streamThatIsntWriteable = new Stream('https://raw.githubusercontent.com/stark-php/http/master/LICENSE');

        $streamWithNoLengthSize = $streamWithNoLength->getSize();
        $streamWithLengthSize = $streamWithLength->getSize();
        $streamThatIsntWriteableSize = $streamThatIsntWriteable->getSize();

        $this->assertEquals(null, $streamWithNoLengthSize);
        $this->assertEquals(52217, $streamWithLengthSize);
        $this->assertEquals(1076, $streamThatIsntWriteableSize);
    }

    public function testWeCanFindOutIfWeAreAtTheEndOfAStream()
    {
        $stream = new Stream('LICENSE');

        $atTheEndOfAStream = $stream->eof();

        $this->assertFalse($atTheEndOfAStream);
    }

    public function testWeCanReadSomeBytesFromTheStream()
    {
        $stream = new Stream('LICENSE');

        $fiveBytesOfInformation = $stream->read(5);

        $this->assertEquals('The M', $fiveBytesOfInformation);
    }

    public function testCheckIfAStreamIsWriteable()
    {
        $streamWithNoLength = new Stream('LICENSE');
        $streamWithLength = new Stream('http://placehold.it/1500x1500');
        $streamThatIsntWriteable = new Stream('https://raw.githubusercontent.com/stark-php/http/master/LICENSE');

        $streamWithNoLengthIsWriteable = $streamWithNoLength->isWritable();
        $streamWithLengthIsWriteable = $streamWithLength->isWritable();
        $streamThatIsntWriteableIsWriteable = $streamThatIsntWriteable->isWritable();

        $this->assertTrue($streamWithNoLengthIsWriteable);
        $this->assertFalse($streamWithLengthIsWriteable);
        $this->assertFalse($streamThatIsntWriteableIsWriteable);
    }

    public function testCheckIfAStreamIsReadable()
    {
        $streamWithNoLength = new Stream('LICENSE');
        $streamWithLength = new Stream('http://placehold.it/1500x1500');
        $streamThatIsntReadable = new Stream('https://raw.githubusercontent.com/stark-php/http/master/LICENSE');

        $streamWithNoLengthIsReadable = $streamWithNoLength->isReadable();
        $streamWithLengthIsReadable = $streamWithLength->isReadable();
        $streamThatIsntReadableIsReadable = $streamThatIsntReadable->isReadable();

        $this->assertTrue($streamWithNoLengthIsReadable);
        $this->assertFalse($streamWithLengthIsReadable);
        $this->assertFalse($streamThatIsntReadableIsReadable);
    }

    public function testCheckIfAStreamIsSeekable()
    {
        $streamWithNoLength = new Stream('LICENSE');
        $streamWithLength = new Stream('http://placehold.it/1500x1500');
        $streamThatIsntSeekable = new Stream('https://raw.githubusercontent.com/stark-php/http/master/LICENSE');

        $streamWithNoLengthIsSeekable = $streamWithNoLength->isSeekable();
        $streamWithLengthIsSeekable = $streamWithLength->isSeekable();
        $streamThatIsntSeekableIsSeekable = $streamThatIsntSeekable->isSeekable();

        $this->assertTrue($streamWithNoLengthIsSeekable);
        $this->assertFalse($streamWithLengthIsSeekable);
        $this->assertFalse($streamThatIsntSeekableIsSeekable);
    }
}

<?php

use Stark\Http\Messages\Stream;

class StreamTest extends PHPUnit_Framework_TestCase
{

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
        $streamThatIsntWriteable = new Stream('http://google.com');

        $streamWithNoLengthSize = $streamWithNoLength->getSize();
        $streamWithLengthSize = $streamWithLength->getSize();
        $streamThatIsntWriteableSize = $streamThatIsntWriteable->getSize();

        $this->assertEquals(null, $streamWithNoLengthSize);
        $this->assertEquals(52217, $streamWithLengthSize);
        $this->assertEquals(259, $streamThatIsntWriteableSize);
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
}

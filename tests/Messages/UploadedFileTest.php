<?php

class UploadedFileTest extends PHPUnit_Framework_TestCase
{
    public function testWeCanGetAStream()
    {
        $file = $this->createMockForFileUpload([
        'name'     => 'foo.txt',
        'type'     => 'text/plain',
        'tmp_name' => 'LICENSE',
        'error'    => 0,
        'size'     => 100,
    ]);

        $this->assertEquals('The MIT License (MIT)', $file->getStream()->read(21));
    }

    public function testWeCanUploadAFile()
    {
        // Because of the mocking this doesn't actually move a file
        // So we test the internals of the class but not against a Filesystem
        // This could be improved
        $this->expectException(RuntimeException::class);

        $file = $this->createMockForFileUpload([
            'name'     => 'foo.txt',
            'type'     => 'text/plain',
            'tmp_name' => '/tmp/foobar',
            'error'    => 0,
            'size'     => 100,
        ]);

        $file->moveTo('build/');

        $file->getStream();
    }

    public function testWeCanGetTheSizeOfTheFile()
    {
        $file = $this->createMockForFileUpload([
            'name'     => 'foo.txt',
            'type'     => 'text/plain',
            'tmp_name' => '/tmp/foobar',
            'error'    => 0,
            'size'     => 100,
        ]);

        $this->assertEquals(100, $file->getSize());
    }

    public function testWeCanGetTheError()
    {
        $file = $this->createMockForFileUpload([
            'name'     => 'foo.txt',
            'type'     => 'text/plain',
            'tmp_name' => '/tmp/foobar',
            'error'    => 4,
            'size'     => 100,
        ]);

        $this->assertEquals(4, $file->getError());
    }

    public function testWeCanGetTheClientFileName()
    {
        $file = $this->createMockForFileUpload([
            'name'     => 'foo.txt',
            'type'     => 'text/plain',
            'tmp_name' => '/tmp/foobar',
            'error'    => 0,
            'size'     => 100,
        ]);

        $this->assertEquals('foo.txt', $file->getClientFilename());
    }

    public function testWeCanGetTheClientMediaType()
    {
        $file = $this->createMockForFileUpload([
            'name'     => 'foo.txt',
            'type'     => 'text/plain',
            'tmp_name' => '/tmp/foobar',
            'error'    => 0,
            'size'     => 100,
        ]);

        $this->assertEquals('text/plain', $file->getClientMediaType());
    }

    protected function createMockForFileUpload(array $file, bool $is_uploaded_file = true, $set_uploaded_file = null)
    {
        $fileMock = $this->getMockBuilder('Stark\Http\Messages\UploadedFile')
            ->setMethods(['is_uploaded_file', 'set_uploaded_file'])
            ->setConstructorArgs([$file])
            ->getMock();

        $fileMock->method('is_uploaded_file')
            ->willReturn($is_uploaded_file);

        $fileMock->method('set_uploaded_file')
            ->willReturn($set_uploaded_file);

        return $fileMock;
    }
}

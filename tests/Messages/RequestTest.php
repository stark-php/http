<?php

use Stark\Http\Messages\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testWeCanGetTrueFromAMethod()
    {
        $request = new Request();

        $result = $request->test();

        $this->assertFalse($result);
    }
}

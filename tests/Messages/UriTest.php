<?php

use Stark\Http\Messages\Uri;

class UriTest extends PHPUnit_Framework_TestCase
{

    public function testWeCanGetTheSchemeOfTheUri()
    {
        $uri = new Uri('https', '', '', 'google.com', 80, '/', '', '');

        $this->assertEquals('https', $uri->getScheme());
    }
}

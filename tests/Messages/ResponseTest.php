<?php

use Stark\Http\Messages\Response;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testWeCanGetTheStatusCode()
    {
        $response = new Response(200);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testWeCanGetTheResponseWithADifferentStatusCode()
    {
        $response = new Response(200);

        $new_instance = $response->withStatus(404);

        $this->assertEquals(404, $new_instance->getStatusCode());
    }

    public function testWeCanGetTheCustomReasonPhrase()
    {
        $response = new Response(200, 'All fine and dandy');

        $this->assertEquals('All fine and dandy', $response->getReasonPhrase());
    }

    public function testWeCanGetTheDefaultReasonPhrase()
    {
        $ok_response        = new Response(200);
        $created_response   = new Response(201);
        $not_found_response = new Response(404);

        $this->assertEquals('OK', $ok_response->getReasonPhrase());
        $this->assertEquals('Created', $created_response->getReasonPhrase());
        $this->assertEquals('Not Found', $not_found_response->getReasonPhrase());
    }

    public function testWeGetAnEmptyStringForAStatusCodeWithNoDefaultReasonPhrase()
    {
        $response = new Response(418);

        $this->assertEquals('', $response->getReasonPhrase());
    }
}

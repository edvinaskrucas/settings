<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

class ContextSerializerTest extends TestCase
{
    public function tearDown():void
    {
        m::close();
    }

    public function testSetContextArgument()
    {
        $context = new \Krucas\Settings\Context();
        $context->set('a', 'a');

        $serializer = new \Krucas\Settings\ContextSerializers\ContextSerializer();

        $this->assertEquals(serialize($context), $serializer->serialize($context));
    }

    public function testSerializeNull()
    {
        $serializer = new \Krucas\Settings\ContextSerializers\ContextSerializer();

        $this->assertEquals(serialize(null), $serializer->serialize(null));
    }
}

<?php

use Mockery as m;

class ValueSerializerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testSerializeMethod()
    {
        $serializer = new \Krucas\Settings\ValueSerializers\ValueSerializer();

        $object = new stdClass();
        $object->a = 'b';

        $this->assertEquals(serialize(null), $serializer->serialize(null));
        $this->assertEquals(serialize(1), $serializer->serialize(1));
        $this->assertEquals(serialize(1.1), $serializer->serialize(1.1));
        $this->assertEquals(serialize('string'), $serializer->serialize('string'));
        $this->assertEquals(serialize(['array' => 'array']), $serializer->serialize(['array' => 'array']));
        $this->assertEquals(serialize($object), $serializer->serialize($object));
    }

    public function testUnserializeMethod()
    {
        $serializer = new \Krucas\Settings\ValueSerializers\ValueSerializer();

        $object = new stdClass();
        $object->a = 'b';

        $value = serialize(null);
        $this->assertEquals(unserialize($value), $serializer->unserialize($value));

        $value = serialize(1);
        $this->assertEquals(unserialize($value), $serializer->unserialize($value));

        $value = serialize(1.1);
        $this->assertEquals(unserialize($value), $serializer->unserialize($value));

        $value = serialize('string');
        $this->assertEquals(unserialize($value), $serializer->unserialize($value));

        $value = serialize(['array' => 'array']);
        $this->assertEquals(unserialize($value), $serializer->unserialize($value));

        $value = serialize($object);
        $this->assertEquals(unserialize($value), $serializer->unserialize($value));
    }
}

<?php

use Mockery as m;

class ContextTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testSetContextArgument()
    {
        $context = new \Krucas\Settings\Context();

        $this->assertCount(0, $context);
        $this->assertFalse($context->has('test'));

        $context->set('test', 'a');

        $this->assertCount(1, $context);
        $this->assertTrue($context->has('test'));
        $this->assertEquals('a', $context->get('test'));

        $context->remove('test');

        $this->assertCount(0, $context);
        $this->assertFalse($context->has('test'));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetUndefinedContextArgument()
    {
        $context = new \Krucas\Settings\Context();
        $context->get('test');
    }
}

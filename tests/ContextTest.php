<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    public function tearDown():void
    {
        m::close();
    }

    public function testConstructorSetsValues()
    {
        $context = new \Krucas\Settings\Context(['test' => 'value', 'a' => 'b']);

        $this->assertCount(2, $context);
        $this->assertEquals('value', $context->get('test'));
        $this->assertEquals('b', $context->get('a'));
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


    public function testGetUndefinedContextArgument()
    {
        $this->expectException(\OutOfBoundsException::class);
        $context = new \Krucas\Settings\Context();
        $context->get('test');
    }
}

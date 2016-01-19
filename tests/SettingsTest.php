<?php

use Mockery as m;

class SettingsTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testHasReturnRepositoryValue()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('has')->with('key_g')->andReturn(true, false);

        $valueSerializer = $this->getValueSerializerMock();

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);

        $this->assertTrue($settings->has('key'));
        $this->assertFalse($settings->has('key'));
    }

    public function testHasFiresEvents()
    {
        $context = new \Krucas\Settings\Context();

        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', $context)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('has')->with('key_g')->andReturn(true);

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->once()->with('settings.checking: key', ['key', $context]);
        $dispatcher->shouldReceive('fire')->once()->with('settings.has: key', ['key', true, $context]);

        $valueSerializer = $this->getValueSerializerMock();

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->enableEvents();
        $settings->setDispatcher($dispatcher);

        $this->assertTrue($settings->context($context)->has('key'));
    }

    public function testHasSkipsEvents()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('has')->with('key_g')->andReturn(true);

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->never();

        $valueSerializer = $this->getValueSerializerMock();

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->disableEvents();
        $settings->setDispatcher($dispatcher);

        $this->assertTrue($settings->has('key'));
    }

    public function testGetReturnsRepositoryValue()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->with('key_g', null)->andReturn('serialized');

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('unserialize')->once()->with('serialized')->andReturn('value');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);

        $this->assertEquals('value', $settings->get('key'));
    }

    public function testGetReturnsDefaultValue()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->with('key_g', 'default')->andReturn(null);

        $valueSerializer = $this->getValueSerializerMock();

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);

        $this->assertEquals('default', $settings->get('key', 'default'));
    }

    public function testGetUsesCache()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->never();

        $cache = $this->getCacheMock();
        $cache->shouldReceive('rememberForever')->once()->andReturn('cached');

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('unserialize')->with('cached')->andReturn('unserialized');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->enableCache();
        $settings->setCache($cache);

        $this->assertEquals('unserialized', $settings->get('key'));
    }

    public function testGetSkipsCache()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->once()->andReturn('serialized');

        $cache = $this->getCacheMock();
        $cache->shouldReceive('rememberForever')->never();

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('unserialize')->with('serialized')->andReturn('value');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->disableCache();
        $settings->setCache($cache);

        $this->assertEquals('value', $settings->get('key'));
    }

    public function testGetUsesEncrypter()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->once()->andReturn('encrypted');

        $encrypter = $this->getEncrypterMock();
        $encrypter->shouldReceive('decrypt')->once()->with('encrypted')->andReturn('serialized');

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('unserialize')->with('serialized')->andReturn('value');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->enableEncryption();
        $settings->setEncrypter($encrypter);

        $this->assertEquals('value', $settings->get('key'));
    }

    public function testGetSkipsEncrypter()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->once()->andReturn('serialized');

        $encrypter = $this->getEncrypterMock();
        $encrypter->shouldReceive('decrypt')->never();

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('unserialize')->with('serialized')->andReturn('value');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->disableEncryption();
        $settings->setEncrypter($encrypter);

        $this->assertEquals('value', $settings->get('key'));
    }

    public function testGetFiresEvents()
    {
        $context = new \Krucas\Settings\Context();

        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', $context)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->once()->with('key_g', 'default')->andReturn('serialized');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->once()->with('settings.getting: key', ['key', 'default', $context]);
        $dispatcher->shouldReceive('fire')->once()->with('settings.get: key', ['key', 'value', 'default', $context]);

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('unserialize')->with('serialized')->andReturn('value');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->enableEvents();
        $settings->setDispatcher($dispatcher);

        $this->assertEquals('value', $settings->context($context)->get('key', 'default'));
    }

    public function testGetSkipsEvents()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->once()->with('key_g', 'default')->andReturn('serialized');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->never();

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('unserialize')->with('serialized')->andReturn('value');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->disableEvents();
        $settings->setDispatcher($dispatcher);

        $this->assertEquals('value', $settings->get('key', 'default'));
    }

    public function testSetSetsValueToRepository()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key_g', 'serialized');

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('serialize')->with('value')->andReturn('serialized');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);

        $settings->set('key', 'value');
    }

    public function testSetUsesEncrypter()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key_g', 'encrypted');

        $encrypter = $this->getEncrypterMock();
        $encrypter->shouldReceive('encrypt')->once()->with('serialized')->andReturn('encrypted');

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('serialize')->with('value')->andReturn('serialized');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->enableEncryption();
        $settings->setEncrypter($encrypter);

        $settings->set('key', 'value');
    }

    public function testSetSkipsEncrypter()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key_g', 'serialized');

        $encrypter = $this->getEncrypterMock();
        $encrypter->shouldReceive('encrypt')->never();

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('serialize')->with('value')->andReturn('serialized');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->disableEncryption();
        $settings->setEncrypter($encrypter);

        $settings->set('key', 'value');
    }

    public function testSetUsesCache()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key_g', 'serialized');

        $cache = $this->getCacheMock();
        $cache->shouldReceive('forget')->once()->with('key_g');

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('serialize')->with('value')->andReturn('serialized');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->enableCache();
        $settings->setCache($cache);

        $settings->set('key', 'value');
    }

    public function testSetSkipsCache()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key_g', 'serialized');

        $cache = $this->getCacheMock();
        $cache->shouldReceive('forget')->never();

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('serialize')->with('value')->andReturn('serialized');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->disableCache();
        $settings->setCache($cache);

        $settings->set('key', 'value');
    }

    public function testSetFiresEvents()
    {
        $context = new \Krucas\Settings\Context();

        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', $context)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key_g', 'serialized');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->once()->with('settings.setting: key', ['key', 'value', $context]);
        $dispatcher->shouldReceive('fire')->once()->with('settings.set: key', ['key', 'value', $context]);

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('serialize')->with('value')->andReturn('serialized');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->enableEvents();
        $settings->setDispatcher($dispatcher);

        $settings->context($context)->set('key', 'value');
    }

    public function testSetSkipsEvents()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key_g', 'serialized');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->never();

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('serialize')->with('value')->andReturn('serialized');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->disableEvents();
        $settings->setDispatcher($dispatcher);

        $settings->set('key', 'value');
    }

    public function testForgetForgetsRepositoryValue()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('forget')->once()->with('key_g');

        $valueSerializer = $this->getValueSerializerMock();

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);

        $settings->forget('key');
    }

    public function testForgetUsesCache()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('forget')->once()->with('key_g');

        $cache = $this->getCacheMock();
        $cache->shouldReceive('forget')->once()->with('key_g');

        $valueSerializer = $this->getValueSerializerMock();

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->enableCache();
        $settings->setCache($cache);

        $settings->forget('key');
    }

    public function testForgetSkipsCache()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('forget')->once()->with('key_g');

        $cache = $this->getCacheMock();
        $cache->shouldReceive('forget')->never();

        $valueSerializer = $this->getValueSerializerMock();

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->disableCache();
        $settings->setCache($cache);

        $settings->forget('key');
    }

    public function testForgetFiresEvents()
    {
        $context = new \Krucas\Settings\Context();

        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', $context)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('forget')->once()->with('key_g');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->once()->with('settings.forgetting: key', ['key', $context]);
        $dispatcher->shouldReceive('fire')->once()->with('settings.forget: key', ['key', $context]);

        $valueSerializer = $this->getValueSerializerMock();

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->enableEvents();
        $settings->setDispatcher($dispatcher);

        $settings->context($context)->forget('key');
    }

    public function testForgetSkipsEvents()
    {
        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', null)->andReturn('key_g');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('forget')->once()->with('key_g');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->never();

        $valueSerializer = $this->getValueSerializerMock();

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);
        $settings->disableEvents();
        $settings->setDispatcher($dispatcher);

        $settings->forget('key');
    }

    public function testSetSameKeysForDifferentContexts()
    {
        $context1 = new \Krucas\Settings\Context();

        $context2 = new \Krucas\Settings\Context();

        $g = $this->getKeyGeneratorMock();
        $g->shouldReceive('generate')->with('key', $context1)->andReturn('key_1');
        $g->shouldReceive('generate')->with('key', $context2)->andReturn('key_2');
        $g->shouldReceive('generate')->with('key', null)->andReturn('key');

        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->with('key_1', 'serialized1');
        $mock->shouldReceive('get')->with('key_1', null)->andReturn('serialized1');
        $mock->shouldReceive('set')->with('key_2', 'serialized2');
        $mock->shouldReceive('get')->with('key_2', null)->andReturn('serialized2');
        $mock->shouldReceive('get')->with('key', null)->andReturn(null);

        $valueSerializer = $this->getValueSerializerMock();
        $valueSerializer->shouldReceive('serialize')->with('v1')->andReturn('serialized1');
        $valueSerializer->shouldReceive('unserialize')->with('serialized1')->andReturn('v1');
        $valueSerializer->shouldReceive('serialize')->with('v2')->andReturn('serialized2');
        $valueSerializer->shouldReceive('unserialize')->with('serialized2')->andReturn('v2');

        $settings = new \Krucas\Settings\Settings($mock, $g, $valueSerializer);

        $settings->context($context1)->set('key', 'v1');
        $settings->context($context2)->set('key', 'v2');

        $this->assertEquals('v1', $settings->context($context1)->get('key'));
        $this->assertEquals('v2', $settings->context($context2)->get('key'));
        $this->assertEquals(null, $settings->get('key'));
    }

    protected function getRepositoryMock()
    {
        return m::mock('Krucas\Settings\Contracts\Repository');
    }

    protected function getDispatcherMock()
    {
        return m::mock('Illuminate\Contracts\Events\Dispatcher');
    }

    protected function getCacheMock()
    {
        return m::mock('Illuminate\Contracts\Cache\Repository');
    }

    protected function getEncrypterMock()
    {
        return m::mock('Illuminate\Contracts\Encryption\Encrypter');
    }

    protected function getKeyGeneratorMock()
    {
        return m::mock('Krucas\Settings\Contracts\KeyGenerator');
    }

    protected function getValueSerializerMock()
    {
        return m::mock('Krucas\Settings\Contracts\ValueSerializer');
    }
}

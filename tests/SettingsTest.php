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
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('has')->with('key')->andReturn(true, false);

        $settings = new \Krucas\Settings\Settings($mock);

        $this->assertTrue($settings->has('key'));
        $this->assertFalse($settings->has('key'));
    }

    public function testHasFiresEvents()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('has')->with('key')->andReturn(true);

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->once()->with('settings.checking: key', ['key']);
        $dispatcher->shouldReceive('fire')->once()->with('settings.has: key', ['key', true]);

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->enableEvents();
        $settings->setDispatcher($dispatcher);

        $this->assertTrue($settings->has('key'));
    }

    public function testHasSkipsEvents()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('has')->with('key')->andReturn(true);

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->never();

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->disableEvents();
        $settings->setDispatcher($dispatcher);

        $this->assertTrue($settings->has('key'));
    }

    public function testGetReturnsRepositoryValue()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->with('key', null)->andReturn('value');

        $settings = new \Krucas\Settings\Settings($mock);

        $this->assertEquals('value', $settings->get('key'));
    }

    public function testGetReturnsDefaultValue()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->with('key', 'default')->andReturn(null);

        $settings = new \Krucas\Settings\Settings($mock);

        $this->assertEquals('default', $settings->get('key', 'default'));
    }

    public function testGetUsesCache()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->never();

        $cache = $this->getCacheMock();
        $cache->shouldReceive('rememberForever')->once()->andReturn('cached');

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->enableCache();
        $settings->setCache($cache);

        $this->assertEquals('cached', $settings->get('key'));
    }

    public function testGetSkipsCache()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->once()->andReturn('value');

        $cache = $this->getCacheMock();
        $cache->shouldReceive('rememberForever')->never();

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->disableCache();
        $settings->setCache($cache);

        $this->assertEquals('value', $settings->get('key'));
    }

    public function testGetUsesEncrypter()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->once()->andReturn('encrypted');

        $encrypter = $this->getEncrypterMock();
        $encrypter->shouldReceive('decrypt')->once()->with('encrypted')->andReturn('value');

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->enableEncryption();
        $settings->setEncrypter($encrypter);

        $this->assertEquals('value', $settings->get('key'));
    }

    public function testGetSkipsEncrypter()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->once()->andReturn('value');

        $encrypter = $this->getEncrypterMock();
        $encrypter->shouldReceive('decrypt')->never();

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->disableEncryption();
        $settings->setEncrypter($encrypter);

        $this->assertEquals('value', $settings->get('key'));
    }

    public function testGetFiresEvents()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->once()->with('key', 'default')->andReturn('value');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->once()->with('settings.getting: key', ['key', 'default']);
        $dispatcher->shouldReceive('fire')->once()->with('settings.get: key', ['key', 'value', 'default']);

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->enableEvents();
        $settings->setDispatcher($dispatcher);

        $this->assertEquals('value', $settings->get('key', 'default'));
    }

    public function testGetSkipsEvents()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('get')->once()->with('key', 'default')->andReturn('value');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->never();

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->disableEvents();
        $settings->setDispatcher($dispatcher);

        $this->assertEquals('value', $settings->get('key', 'default'));
    }

    public function testSetSetsValueToRepository()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key', 'value');

        $settings = new \Krucas\Settings\Settings($mock);

        $settings->set('key', 'value');
    }

    public function testSetUsesEncrypter()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key', 'encrypted');

        $encrypter = $this->getEncrypterMock();
        $encrypter->shouldReceive('encrypt')->once()->with('value')->andReturn('encrypted');

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->enableEncryption();
        $settings->setEncrypter($encrypter);

        $settings->set('key', 'value');
    }

    public function testSetSkipsEncrypter()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key', 'value');

        $encrypter = $this->getEncrypterMock();
        $encrypter->shouldReceive('encrypt')->never();

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->disableEncryption();
        $settings->setEncrypter($encrypter);

        $settings->set('key', 'value');
    }

    public function testSetUsesCache()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key', 'value');

        $cache = $this->getCacheMock();
        $cache->shouldReceive('forget')->once()->with('key');

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->enableCache();
        $settings->setCache($cache);

        $settings->set('key', 'value');
    }

    public function testSetSkipsCache()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key', 'value');

        $cache = $this->getCacheMock();
        $cache->shouldReceive('forget')->never();

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->disableCache();
        $settings->setCache($cache);

        $settings->set('key', 'value');
    }

    public function testSetFiresEvents()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key', 'value');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->once()->with('settings.setting: key', ['key', 'value']);
        $dispatcher->shouldReceive('fire')->once()->with('settings.set: key', ['key', 'value']);

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->enableEvents();
        $settings->setDispatcher($dispatcher);

        $settings->set('key', 'value');
    }

    public function testSetSkipsEvents()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('set')->once()->with('key', 'value');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->never();

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->disableEvents();
        $settings->setDispatcher($dispatcher);

        $settings->set('key', 'value');
    }

    public function testForgetForgetsRepositoryValue()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('forget')->once()->with('key');

        $settings = new \Krucas\Settings\Settings($mock);

        $settings->forget('key');
    }

    public function testForgetUsesCache()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('forget')->once()->with('key');

        $cache = $this->getCacheMock();
        $cache->shouldReceive('forget')->once()->with('key');

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->enableCache();
        $settings->setCache($cache);

        $settings->forget('key');
    }

    public function testForgetSkipsCache()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('forget')->once()->with('key');

        $cache = $this->getCacheMock();
        $cache->shouldReceive('forget')->never();

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->disableCache();
        $settings->setCache($cache);

        $settings->forget('key');
    }

    public function testForgetFiresEvents()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('forget')->once()->with('key');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->once()->with('settings.forgetting: key', ['key']);
        $dispatcher->shouldReceive('fire')->once()->with('settings.forget: key', ['key']);

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->enableEvents();
        $settings->setDispatcher($dispatcher);

        $settings->forget('key');
    }

    public function testForgetSkipsEvents()
    {
        $mock = $this->getRepositoryMock();
        $mock->shouldReceive('forget')->once()->with('key');

        $dispatcher = $this->getDispatcherMock();
        $dispatcher->shouldReceive('fire')->never();

        $settings = new \Krucas\Settings\Settings($mock);
        $settings->disableEvents();
        $settings->setDispatcher($dispatcher);

        $settings->forget('key');
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
}

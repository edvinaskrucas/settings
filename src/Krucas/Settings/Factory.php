<?php namespace Krucas\Settings;

use Closure;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Krucas\Settings\Contracts\Factory as FactoryContract;
use Krucas\Settings\Repositories\DatabaseRepository;

class Factory implements FactoryContract
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved repositories.
     *
     * @var array
     */
    protected $repositories = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Create a new Factory instance.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a setting repository instance by name.
     *
     * @param string|null $name
     * @return mixed
     */
    public function repository($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->repositories[$name] = $this->get($name);
    }

    /**
     * Get a settings driver instance.
     *
     * @param string $driver
     * @return mixed
     */
    public function driver($driver = null)
    {
        return $this->repository($driver);
    }

    /**
     * Attempt to get the repository from the local cache.
     *
     * @param string $name
     * @return \Krucas\Settings\Contracts\Repository
     */
    protected function get($name)
    {
        return isset($this->repositories[$name]) ? $this->repositories[$name] : $this->resolve($name);
    }

    /**
     * Resolve the given repository.
     *
     * @param string $name
     * @return \Krucas\Settings\Contracts\Repository
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Settings repository [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        } else {
            return $this->{'create'.ucfirst($config['driver']).'Driver'}($config);
        }
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * Create database repository.
     *
     * @param array $config
     * @return \Krucas\Settings\Repositories\DatabaseRepository
     */
    protected function createDatabaseDriver(array $config)
    {
        return new DatabaseRepository(
            $this->app['db']->connection(Arr::get($config, 'connection')),
            Arr::get($config, 'table')
        );
    }

    /**
     * Get the settings driver configuration.
     *
     * @param string $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["settings.repositories.{$name}"];
    }

    /**
     * Get the default settings repository name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['settings.default'];
    }

    /**
     * Set the default settings repository name.
     *
     * @param string $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['settings.default'] = $name;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string $driver
     * @param \Closure $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->repository(), $method], $parameters);
    }
}

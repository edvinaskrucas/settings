<?php

namespace Krucas\Settings;

class Context implements \Countable
{
    /**
     * Array of context arguments.
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * @param array $arguments
     */
    public function __construct(array $arguments = [])
    {
        foreach ($arguments as $name => $value) {
            $this->set($name, $value);
        }
    }

    /**
     * Access context argument value.
     *
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if (!isset($this->arguments[$name])) {
            throw new \OutOfBoundsException(sprintf('"%s" is not part of context.', $name));
        }

        return $this->arguments[$name];
    }

    /**
     * Set context argument value.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set($name, $value)
    {
        $this->arguments[$name] = $value;
    }

    /**
     * Determine if context argument is set or not.
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->arguments[$name]);
    }

    /**
     * Unset given context argument.
     *
     * @param string $name
     * @return void
     */
    public function remove($name)
    {
        unset($this->arguments[$name]);
    }

    /**
     * Return count of context arguments.
     *
     * @return int
     */
    public function count()
    {
        return count($this->arguments);
    }
}

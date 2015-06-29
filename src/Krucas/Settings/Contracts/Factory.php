<?php namespace Krucas\Settings\Contracts;

interface Factory
{
    /**
     * Get a setting repository instance by name.
     *
     * @param string|null $name
     * @return mixed
     */
    public function repository($name = null);
}

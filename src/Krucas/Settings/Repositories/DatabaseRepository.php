<?php namespace Krucas\Settings\Repositories;

use Exception;
use Illuminate\Database\Connection;
use Krucas\Settings\Contracts\Repository;

class DatabaseRepository implements Repository
{
    /**
     * Database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * Database table to store settings.
     *
     * @var string
     */
    protected $table;

    /**
     * Create new database repository.
     *
     * @param \Illuminate\Database\Connection $connection
     * @param string $table
     */
    public function __construct(Connection $connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Determine if the given setting value exists.
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->table()->where('key', '=', $key)->count() > 0 ? true : false;
    }

    /**
     * Get the specified setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $value = $this->table()->where('key', '=', $key)->value('value');

        return is_null($value) ? $default : $value;
    }

    /**
     * Set a given setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value = null)
    {
        try {
            $this->table()->insert(compact('key', 'value'));
        } catch (Exception $e) {
            $this->table()->where('key', '=', $key)->update(compact('value'));
        }
    }

    /**
     * Forget current setting value.
     *
     * @param string $key
     * @return void
     */
    public function forget($key)
    {
        $this->table()->where('key', $key)->delete();
    }

    /**
     * Get a query builder for the settings table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table()
    {
        return $this->connection->table($this->table);
    }
}

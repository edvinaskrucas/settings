<?php namespace Krucas\Settings;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Events\Dispatcher;
use Krucas\Settings\Contracts\KeyGenerator;
use Krucas\Settings\Contracts\Repository;
use Illuminate\Contracts\Cache\Repository as Cache;
use Krucas\Settings\Contracts\ValueSerializer;

class Settings implements Repository
{
    /**
     * Settings repository.
     *
     * @var \Krucas\Settings\Contracts\Repository
     */
    protected $repository;

    /**
     * Repository key generator.
     *
     * @var \Krucas\Settings\Contracts\KeyGenerator
     */
    protected $keyGenerator;

    /**
     * Cache repository.
     *
     * @var null|\Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Encrypter instance to encrypt settings.
     *
     * @var null|\Illuminate\Contracts\Encryption\Encrypter
     */
    protected $encrypter;

    /**
     * Event dispatcher instance.
     *
     * @var null|\Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Enable cache.
     *
     * @var bool
     */
    protected $cacheEnabled = false;

    /**
     * Enable encryption.
     *
     * @var bool
     */
    protected $encryptionEnabled = false;

    /**
     * Enable events.
     *
     * @var bool
     */
    protected $eventsEnabled = false;

    /**
     * Used context.
     *
     * @var \Krucas\Settings\Context
     */
    protected $context;

    /**
     * Value serializer.
     *
     * @var \Krucas\Settings\Contracts\ValueSerializer
     */
    protected $valueSerializer;

    /**
     * Create new settings.
     *
     * @param \Krucas\Settings\Contracts\Repository $repository
     * @param \Krucas\Settings\Contracts\KeyGenerator $keyGenerator
     * @param \Krucas\Settings\Contracts\ValueSerializer $valueSerializer
     */
    public function __construct(Repository $repository, KeyGenerator $keyGenerator, ValueSerializer $valueSerializer)
    {
        $this->repository = $repository;
        $this->keyGenerator = $keyGenerator;
        $this->valueSerializer = $valueSerializer;
    }

    /**
     * Return wrapped repository instance.
     *
     * @return \Krucas\Settings\Contracts\Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Return repository key generator.
     *
     * @return \Krucas\Settings\Contracts\KeyGenerator
     */
    public function getKeyGenerator()
    {
        return $this->keyGenerator;
    }

    /**
     * Enable cache.
     *
     * @return void
     */
    public function enableCache()
    {
        $this->cacheEnabled = true;
    }

    /**
     * Disable cache.
     *
     * @return void
     */
    public function disableCache()
    {
        $this->cacheEnabled = false;
    }

    /**
     * Set cache store.
     *
     * @param \Illuminate\Contracts\Cache\Repository $cache
     * @return void
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Return cache store instance.
     *
     * @return \Illuminate\Contracts\Cache\Repository|null
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Determines if cache is enabled or not.
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->cacheEnabled && !is_null($this->cache) ? true : false;
    }

    /**
     * Enable value encryption.
     *
     * @return void
     */
    public function enableEncryption()
    {
        $this->encryptionEnabled = true;
    }

    /**
     * Disable value encryption.
     *
     * @return void
     */
    public function disableEncryption()
    {
        $this->encryptionEnabled = false;
    }

    /**
     * Set value encrypter.
     *
     * @param \Illuminate\Contracts\Encryption\Encrypter $encrypter
     * @return void
     */
    public function setEncrypter(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    /**
     * Return encrypter instance.
     *
     * @return \Illuminate\Contracts\Encryption\Encrypter|null
     */
    public function getEncrypter()
    {
        return $this->encrypter;
    }

    /**
     * Determine if encryption is enabled or not.
     *
     * @return bool
     */
    public function isEncryptionEnabled()
    {
        return $this->encryptionEnabled && !is_null($this->encrypter) ? true : false;
    }

    /**
     * Enable events.
     *
     * @return void
     */
    public function enableEvents()
    {
        $this->eventsEnabled = true;
    }

    /**
     * Disable events.
     *
     * @return void
     */
    public function disableEvents()
    {
        $this->eventsEnabled = false;
    }

    /**
     * Set events dispatcher.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     * @return void
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Return event dispatcher instance.
     *
     * @return \\Illuminate\Contracts\Events\Dispatcher|null
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Determine if events is enabled or not.
     *
     * @return bool
     */
    public function isEventsEnabled()
    {
        return $this->eventsEnabled && !is_null($this->dispatcher) ? true : false;
    }

    /**
     * Set or reset context.
     *
     * @param \Krucas\Settings\Context|null $context
     * @return $this
     */
    public function context(Context $context = null)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Determine if the given setting value exists.
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $this->fire('checking', $key, [$key]);

        $status = $this->repository->has($this->getKey($key));

        $this->fire('has', $key, [$key, $status]);

        $this->context(null);

        return $status;
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
        $this->fire('getting', $key, [$key, $default]);

        $generatedKey = $this->getKey($key);

        if ($this->isCacheEnabled()) {
            $repository = $this->repository;

            $value = $this->cache->rememberForever($generatedKey, function () use ($repository, $generatedKey) {
                return $repository->get($generatedKey);
            });
        } else {
            $value = $this->repository->get($generatedKey, $default);
        }

        if (!is_null($value)) {
            $value = $this->unserializeValue($this->isEncryptionEnabled() ? $this->encrypter->decrypt($value) : $value);
        } else {
            $value = $default;
        }

        $this->fire('get', $key, [$key, $value, $default]);

        $this->context(null);

        return $value;
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
        $this->fire('setting', $key, [$key, $value]);

        $generatedKey = $this->getKey($key);

        $serializedValue = $this->serializeValue($value);

        $this->repository->set(
            $generatedKey,
            $this->isEncryptionEnabled()? $this->encrypter->encrypt($serializedValue) : $serializedValue
        );

        if ($this->isCacheEnabled()) {
            $this->cache->forget($generatedKey);
        }

        $this->fire('set', $key, [$key, $value]);

        $this->context(null);
    }

    /**
     * Forget current setting value.
     *
     * @param string $key
     * @return void
     */
    public function forget($key)
    {
        $this->fire('forgetting', $key, [$key]);

        $generatedKey = $this->getKey($key);

        $this->repository->forget($generatedKey);

        if ($this->isCacheEnabled()) {
            $this->cache->forget($generatedKey);
        }

        $this->fire('forget', $key, [$key]);

        $this->context(null);
    }

    /**
     * Return repository cache key.
     *
     * @param string $key
     * @return string
     */
    protected function getKey($key)
    {
        return $this->keyGenerator->generate($key, $this->context);
    }

    /**
     * Unserialize given serialized value.
     *
     * @param mixed $serialized
     * @return mixed
     */
    protected function unserializeValue($serialized)
    {
        return $this->valueSerializer->unserialize($serialized);
    }

    /**
     * Serialize given value.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function serializeValue($value)
    {
        return $this->valueSerializer->serialize($value);
    }

    /**
     * Fire settings event.
     *
     * @param string $event
     * @param string $key
     * @param array $payload
     */
    protected function fire($event, $key, array $payload = [])
    {
        $payload[] = $this->context;

        if ($this->isEventsEnabled()) {
            $this->dispatcher->fire("settings.{$event}: {$key}", $payload);
        }
    }
}

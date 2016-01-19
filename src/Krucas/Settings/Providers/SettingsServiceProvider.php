<?php namespace Krucas\Settings\Providers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Krucas\Settings\Console\SettingsTableCommand;
use Krucas\Settings\Factory;
use Krucas\Settings\Settings;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @param \Krucas\Settings\Settings $settings
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    public function boot(Settings $settings, Repository $config, Dispatcher $dispatcher)
    {
        $this->publishes([
            __DIR__ . '/../../../config/settings.php' => config_path('settings.php'),
        ], 'config');

        $override = $config->get('settings.override', []);

        $dispatcher->listen(
            'settings.override: app.timezone',
            function ($configKey, $configValue, $settingKey, $settingValue) {
                date_default_timezone_set($settingValue);
            }
        );

        if (count($override) > 0) {
            $this->overrideConfig($override, $config, $settings, $dispatcher);
        }
    }

    /**
     * Override give config values from persistent setting storage.
     *
     * @param array $override
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Krucas\Settings\Settings $settings
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    protected function overrideConfig(array $override, Repository $config, Settings $settings, Dispatcher $dispatcher)
    {
        foreach ($override as $key => $settingKey) {
            $configKey = is_string($key) ? $key : $settingKey;

            $dispatcher->fire("settings.overriding: {$configKey}", [$configKey, $settingKey]);

            $settingValue = $settings->get($settingKey);
            $configValue = $config->get($configKey);

            $config->set($configKey, $settingValue);

            $dispatcher->fire("settings.override: {$configKey}", [
                $configKey, $configValue, $settingKey, $settingValue
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/settings.php', 'settings');

        $this->app->singleton('settings.key_generator', function ($app) {
            return $app->make($app['config']['settings.key_generator']);
        });

        $this->app->singleton('settings.context_serializer', function ($app) {
            return $app->make($app['config']['settings.context_serializer']);
        });

        $this->app->singleton('settings.value_serializer', function ($app) {
            return $app->make($app['config']['settings.value_serializer']);
        });

        $this->app->singleton('settings.factory', function ($app) {
            return new Factory($app);
        });

        $this->app->singleton('settings.repository', function ($app) {
            return $app['settings.factory']->driver();
        });

        $this->app->singleton('settings', function ($app) {
            $settings = new Settings(
                $app['settings.repository'],
                $app['settings.key_generator'],
                $app['settings.value_serializer']
            );

            $settings->setCache($app['cache.store']);
            $settings->setEncrypter($app['encrypter']);
            $settings->setDispatcher($app['events']);

            $app['config']['settings.cache'] ? $settings->enableCache() : $settings->disableCache();
            $app['config']['settings.encryption'] ? $settings->enableEncryption() : $settings->disableEncryption();
            $app['config']['settings.events'] ? $settings->enableEvents() : $settings->disableEvents();

            return $settings;
        });

        $this->registerAliases();

        $this->registerCommands();
    }

    /**
     * Register aliases.
     *
     * @return void
     */
    protected function registerAliases()
    {
        $this->app->alias('settings.factory', 'Krucas\Settings\Contracts\Factory');

        $this->app->alias('settings.repository', 'Krucas\Settings\Contracts\Repository');

        $this->app->alias('settings.key_generator', 'Krucas\Settings\Contracts\KeyGenerator');

        $this->app->alias('settings.context_serializer', 'Krucas\Settings\Contracts\ContextSerializer');

        $this->app->alias('settings.value_serializer', 'Krucas\Settings\Contracts\ValueSerializer');

        $this->app->alias('settings', 'Krucas\Settings\Settings');
    }

    /**
     * Register the settings related console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->app->singleton('command.settings.table', function ($app) {
            return new SettingsTableCommand($app['files'], $app['composer']);
        });

        $this->commands('command.settings.table');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'settings',
            'settings.repository',
            'settings.factory',
            'settings.key_generator',
            'settings.context_serializer',
            'command.settings.table',
        ];
    }
}

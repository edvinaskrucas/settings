# Persistent Settings for Laravel 5

[![Build Status](https://travis-ci.org/edvinaskrucas/settings.png?branch=master)](https://travis-ci.org/edvinaskrucas/settings)

---

Persistent settings package for Laravel 5.

---

* Driver support
* Cache settings via laravel cache
* Encrypt / Decrypt setting values
* Fire events after action
* Override config values
* Helper function

---

## Installation

Require this package in your composer.json:

```
"require": {
  "edvinaskrucas/settings": "1.0.0"
}
```

### Registering to use it with laravel

Add following lines to ```app/config/app.php```

ServiceProvider array

```php
'Krucas\Settings\Providers\SettingsServiceProvider'
```

Alias array
```php
'Settings' => 'Krucas\Settings\Facades\Settings'
```

### Publishing config file

If you want to edit default config file, just publish it to your app folder.

    php artisan vendor:publish --provider="Krucas\Settings\Providers\SettingsServiceProvider" --tag="config"

## Usage

### Configuration

Package comes with several configuration options.

| Setting | Description |
| --- | --- |
| ```driver``` | Setting repository driver. |
| ```cache``` | Enable or disable setting cache. |
| ```prefix``` | Cache key prefix. |
| ```encryption``` | Enable or disable setting value encryption. |
| ```events``` | Enable or disable event firing. |
| ```repositories``` | Config of all repositories which can be used. |
| ```override``` | Allows you to override values in Laravel config array. |

### Creating table for database driver

To use database driver you have to create table in your database.
Package provides default table migration, to create it you need to execute artisan command:
```
$ php artisan settings:table
```

### Methods

#### Set value

Set setting value.

```php
Settings::set($key, $value = null);
```

#### Get value

Get setting value, default value is returned when no value found.

```php
Settings::get($key, $default = null);
```

#### Check value

Determine if setting exists.

```php
Settings::has($key);
```

#### Forget value

Forget setting value from repository.

```php
Settings::forget($key);
```

### Helpers

#### Settings service instance

Resolve settings service instance.

```php
settings();
```

#### Set value

Set setting value.

```php
settings([$key => $value]);
```

#### Get value

Get setting value, default value is returned when no value found.

```php
settings($key, $default = null);
```

### Events

Events gets fired if this is not disabled via config (enabled by default).

#### settings.checking: $key

Fired before checking if value is present in repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |

#### settings.has: $key

Fired after checking if value is present in repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $status | bool | If setting exists ```true``` is passed, otherwise ```false``` |

#### settings.getting: $key

Fired before retrieving value from repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $default | mixed | Default setting value. |

#### settings.get: $key

Fired after retrieving value from repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $value | mixed | Retrieved setting value. |
| $default | mixed | Default setting value. |

#### settings.setting: $key

Fired before setting value to repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $value | mixed | Setting value to be set. |

#### settings.set: $key

Fired after setting value to repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $value | mixed | Setting value to be set. |

#### settings.forgetting: $key

Fired before forgetting value.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |

#### settings.forget: $key

Fired after forgetting value.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |

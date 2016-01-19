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
* Settings by context
* Serialize values

---

## Installation

Require this package in your composer.json:

```
"require": {
  "edvinaskrucas/settings": "2.0.0"
}
```

### Version matrix

| Laravel Version       | Package version          |
| --------------------- | ------------------------ |
| >=5.0, <=5.1          | >= 1.0.0, <= 2.0.0       |
| 5.2                   | >= 2.0.0                 |

### Registering to use it with laravel

Add following lines to ```app/config/app.php```

ServiceProvider array

```php
Krucas\Settings\Providers\SettingsServiceProvider::class,
```

Alias array
```php
'Settings' => Krucas\Settings\Facades\Settings::class
```

### Publishing config file

If you want to edit default config file, just publish it to your app folder.

    php artisan vendor:publish --provider="Krucas\Settings\Providers\SettingsServiceProvider" --tag="config"

## Usage

### Configuration

Package comes with several configuration options.

| Setting | Description |
| --- | --- |
| ```default``` | Setting repository driver. |
| ```cache``` | Enable or disable setting cache. |
| ```encryption``` | Enable or disable setting value encryption. |
| ```events``` | Enable or disable event firing. |
| ```repositories``` | Config of all repositories which can be used. |
| ```key_generator``` | Key generator class. |
| ```context_serializer``` | Context serializer class. |
| ```value_serializer``` | Value serializer class. |
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

#### Set context

Setting values may be used in certain context. Context can be set using method ```context()```.

```php
Settings::context(new Context(['user' => 1]));
```

Context is reset after call of one these methods ```set```, ```get```, ```has```, ```forget```.
Example how to use settings for different contexts.

```php
$userContext1 = new Context(['user' => 1]);
$userContext2 = new Context(['user' => 2]);
Settings::context($userContext1)->set('key', 'value1');
Settings::context($userContext2)->set('key', 'value2');

// retrieve settings
$userValue1 = Settings::context($userContext1)->get('key'); // value1
$userValue2 = Settings::context($userContext2)->get('key'); // value2
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

Set setting value for a context.

```php
settings([$key => $value], new Context(['user' => 1]));
```

#### Get value

Get setting value, default value is returned when no value found.

```php
settings($key, $default = null);
```

Getting value for a context.

```php
settings($key, $default, new Context(['user' => 1]));
```

### Events

Events gets fired if this is not disabled via config (enabled by default).

#### settings.checking: $key

Fired before checking if value is present in repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $context | null or Context | Setting context. |

#### settings.has: $key

Fired after checking if value is present in repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $status | bool | If setting exists ```true``` is passed, otherwise ```false``` |
| $context | null or Context | Setting context. |

#### settings.getting: $key

Fired before retrieving value from repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $default | mixed | Default setting value. |
| $context | null or Context | Setting context. |

#### settings.get: $key

Fired after retrieving value from repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $value | mixed | Retrieved setting value. |
| $default | mixed | Default setting value. |
| $context | null or Context | Setting context. |

#### settings.setting: $key

Fired before setting value to repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $value | mixed | Setting value to be set. |
| $context | null or Context | Setting context. |

#### settings.set: $key

Fired after setting value to repository.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $value | mixed | Setting value to be set. |
| $context | null or Context | Setting context. |

#### settings.forgetting: $key

Fired before forgetting value.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $context | null or Context | Setting context. |

#### settings.forget: $key

Fired after forgetting value.

| Parameter | Type | Parameter description |
| --- | --- | --- |
| $key | string | Setting key. |
| $context | null or Context | Setting context. |

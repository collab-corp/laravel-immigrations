# Data migrations for Artisans

[![Latest Version on Packagist](https://img.shields.io/packagist/v/collab-corp/laravel-immigrations.svg?style=flat-square)](https://packagist.org/packages/collab-corp/laravel-immigrations)
[![Build Status](https://img.shields.io/travis/collab-corp/laravel-immigrations/master.svg?style=flat-square)](https://travis-ci.org/collab-corp/laravel-immigrations)
[![Quality Score](https://img.shields.io/scrutinizer/g/collab-corp/laravel-immigrations.svg?style=flat-square)](https://scrutinizer-ci.com/g/collab-corp/laravel-immigrations)
[![Total Downloads](https://img.shields.io/packagist/dt/collab-corp/laravel-immigrations.svg?style=flat-square)](https://packagist.org/packages/collab-corp/laravel-immigrations)

When it comes to database migrations, we often tend to think of it as something that'll run once and never be opened again.

Rarely is that the case.

This package provides an easy & elegant solution to that problem.
![Alt Text](https://media.giphy.com/media/583AMBvBSxpOjbHTSb/giphy.gif)
- https://asciinema.org/a/1z4cJdZLOhtq75w8r2VHC3q0M

## Installation

You can install the package via composer:

```bash
composer require collab-corp/laravel-immigrations
```

Since composer isn't configured to look for classes in the ```database_path('Immigrations')``` path by default, you may need to add it in your composer.json autoload.classmap.
```
    // ...
    "autoload": {
        "classmap": [
            "database/Immigrations",
            "database/seeds",
            "database/factories"
        ],
        // ...
    }
```

## Usage
To create a new immigration, simply type
```php
php artisan make:immigration
```

To run the immigrations, type
``` php artisan db:immigration```

The command optionally accepts:

```
--from=connection_name
```
allowing the developer to specify the database connection to load the data from.

## Customizing

### Tl;Dr
Here's a full example. :)
```php
<?php

namespace Database\Immigrations;

use App\AccountingProvider;
use App\User;
use App\UserType;
use CollabCorp\LaravelImmigrations\Database;
use CollabCorp\LaravelImmigrations\Immigration;
use CollabCorp\LaravelImmigrations\Queue;

class CopyUsers implements Immigration
{
    public function __construct(Database $database)
    {
        if ($database->count('users', 'id') === User::query()->count('id')) {
            $this->hasBeenExecuted = true;
        }
    }

    public function shouldRun(Queue $queue): bool
    {
        return User::query()->count('id') === 0;
    }

    public function run(Database $database): void
    {
        $adminEmails = [
            'admin@example.com'
        ];

        $database->each('users', function ($user) use ($adminEmails, $database) {
            /** @var User $newUser */
            $newUser = User::query()->forceCreate([
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name ?? $user->username,
                'type' => UserType::determine($user),
                'email' => $user->email,
                'password' => $user->password,
                'remember_token' => $user->remember_token,
                'photo_url' => $user->photo_url,
                'address' => $user->address,
                'country_code' => $user->country_code,
                'phone' => $user->phone,
                'email_verified_at' => $user->email_verified ? date('Y-m-d H:i:s') : null,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'deleted_at' => $user->deleted_at
            ]);

            $newUser->accountings()->create([
                'provider' => AccountingProvider::Economic,
                'provider_account_id' => $user->economic_customer_number,
            ]);
        });
    }
}
```

### Customizing queries
The ```$database``` instance passed to the Immigration provides conveniently delegates to the underlying Laravel database connection or builder and the each method accepts both a plain string and an query builder instance.

Giving the developer freedom to customize a query builder and pass it to the ``each`` method.

``` php
    public function run(Database $database): void
    {
        $users = $database->table('users')->join('details', 'details.user_id', '=', 'users.id');
    
        $database->each($users, function ($user) use ($database) {
            // ...
        });
```

### database order
An Immigration may configure the database row order by defining the $orderBy and $orderDirection properties.
* These properties takes precedence over a customized query builder.

```php
class CopyUsers implements Immigration
{
    /**
     * The column to order the database rows by
     *
     * @var string
     **/
    public $orderBy = 'created_at';
    
    /**
     * The direction the database should order rows by
     *
     * @var string
     **/
    public $orderDirection = 'asc';
}
```

### Skipping Immigrations
In additional to defining a database order, it is also possible to declare a Immigration as previously executed.
```php
class CopyUsers implements Immigration
{
    /**
     * Whether the immigration has already run
     *
     * @var boolean
     **/
    public $hasBeenExecuted = false;
       
    /**
     * Create a new instance of CopyUsers
     * 
     * @param \CollabCorp\LaravelImmigrations\Database $database
     **/
    public function __construct(Database $database)
    {
        if ($database->count('users', 'id') === User::query()->count('id')) {
            $this->hasBeenExecuted = true;
        }
    }
}
```
Unlike the ```shouldRun(): bool``` method defined by the contract,  marking it as already executed, adds it to the ```$executed``` array.

Allowing the developer to check whether a previous Immigration has run.
```php
class CopyUsers implements Immigration
{
    /**
     * Whether the immigration should run
     *
     * @param Queue $immigrations
     * @return bool
     */
    public function shouldRun(Queue $immigrations): bool
    {
        if (! $immigrations->executed(AnotherImmigration::class)) {
            return false;
        }
    
        return User::query()->count('id') === 0;
    }
}
```

### Terminal output
By default, the current immigration and a progress bar will be outputted to the terminal.

Developers can override the binding in their ServiceProvider of choice, if they see fit.

#### Written output
By rebinding the Writer contract, we can override the std terminal output.
```php
<?php

use Illuminate\Support\ServiceProvider;
use \CollabCorp\LaravelImmigrations\Contracts\Writer as WriterContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // ... Bindings etc
        
        $this->app->bind(WriterContract::class, function () {
            return new MyOutputWriter;
        });
    }
}
```

#### Progress bar
This is a little indirect, bear with me.
To override the progressBar, beyond the published config file
we'll need to override the QueryProcessor.
```php
<?php

use Illuminate\Support\ServiceProvider;
use \CollabCorp\LaravelImmigrations\Contracts\QueryProcessor as QueryProcessorContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // ... Bindings etc
        
        $this->app->bind(QueryProcessorContract::class, function () {
            return new MyQueryProcessor;
        });
    }
}
```

## Configuration

See the published ```config_path('immigrations.php')``` file.

## Registration

To register your immigrations, you may add em in your ServiceProvider of choice.
```php 
\Immigrations::register([...Immigration::class]);
``` 
by default, they run in the chronological order.


### Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email jonas.kerwin.hansen@gmail.com instead of using the issue tracker.

## Credits

- [Jonas Kervin Hansen](https://github.com/sasin91)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

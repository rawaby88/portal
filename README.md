# Portal ⾨

This package adding new guard ['portal'] to use on authenticated routes.

it will check if user has valid Token and permissions to endpoint
## Installation

You can install the package via composer:

```bash
composer require rawaby88/portal
```

## Usage

Add Portable trait to User model;

```php
use Rawaby88\Portal\Portable;


use Portable;
```


Run migrate to update user table:

```bash
php artisan migrate
```
Migration Customization

If you are not going to use Portal's default migrations, 
you should call the 
```bash
Portal::ignoreMigrations();
```
 method in the register method of your 
 
```bash
App\Providers\AppServiceProvider class.
```
You may export the default migrations by executing the following command: 
```bash
 php artisan vendor:publish --tag=portal-migrations
```

Usage by adding middleware 'auth:portal' to endpoints

```php
Route::middleware('auth:portal')->get('/user', function (Request $request) {
    return auth()->user()->token;
});
```
 auth() functions will be available for you to use

```php
auth()->check();
auth()->id();
auth()->user();
auth()->user()->token;
auth()->user()->appliance;

//you can also call other param as credit
auth()->user()->credit;
auth()->user()->workspace;
```

if you would like to change data stored in user table
You may export the default config by executing the following command:
```bash
 php artisan vendor:publish --tag=portal-config
```

[/config/portal.php]
```php
return [
	'auth_endpoint'       => env( 'PORTAL_AUTH_ENDPOINT', '172.17.0.1/api/auth/token/check/' ),
	'expiration'          => null,
	
	//user mode namespace
	'user_model'          => env( 'PORTAL_USER_MODEL', 'App\Models\User' ),
	
	// Mapping data from response to database
    //add or remove fields as you wish   
	'db_user_fields'      => [
		'id'    => 'user_id',
		'name'  => 'name',
		'email' => 'email'
	],
	
	// primary key for user table
	'user_model_key'      => 'user_id',
	
	// primary key type -- don't change
	'user_model_key_type' => 'string',
];
```

### Security

If you discover any security related issues, please email github@dreamod.pl instead of using the issue tracker.

## Credits

-   [Mahmoud Osman](https://github.com/rawaby88)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
# Pwned Passwords

[![Latest Version](https://img.shields.io/github/release/ubient/laravel-pwned-passwords.svg?style=flat-square)](https://github.com/ubient/laravel-pwned-passwords/releases)
[![Build Status](https://img.shields.io/travis/ubient/laravel-pwned-passwords/master.svg?style=flat-square)](https://travis-ci.org/ubient/laravel-pwned-passwords)
[![Quality Score](https://img.shields.io/scrutinizer/g/ubient/laravel-pwned-passwords.svg?style=flat-square)](https://scrutinizer-ci.com/g/ubient/laravel-pwned-passwords)
[![StyleCI](https://styleci.io/repos/151966705/shield)](https://styleci.io/repos/151966705)
[![Total Downloads](https://img.shields.io/packagist/dt/ubient/laravel-pwned-passwords.svg?style=flat-square)](https://packagist.org/packages/ubient/laravel-pwned-passwords)

This package provides a Laravel validation rule that can be used to check a password
against TroyHunt's [Pwned Passwords (haveibeenpwned.com)](https://haveibeenpwned.com/Passwords),
a database containing 517,238,891 real world passwords previously exposed in data breaches.

By using this validation rule, you can prevent re-use of passwords that are unsuitable for ongoing usage,
resulting in a more secure application, as your users will have a much lower risk of having their accounts taken over.

##### How it works

In order to protect the value of the source password being searched for, Pwned Passwords implements a [k-Anonymity model](https://en.wikipedia.org/wiki/K-anonymity) that allows a password to be searched for by partial hash.
This works by hashing the source password with SHA-1, and only sending the first 5 characters of that hash to the API.
By checking whether the rest of the SHA-1 hash occurs within the output, we can verify both whether the password was pwned previously, and how frequently.

## Installation

You can install the package via composer:

```bash
composer require ubient/laravel-pwned-passwords
```

## Usage

Here's a few short examples of what you can do:

```php
/**
 * Get a validator for an incoming registration request.
 *
 * @param  array  $data
 * @return \Illuminate\Contracts\Validation\Validator
 */
protected function validator(array $data)
{
    return Validator::make($data, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed|pwned',
    ]);
}
```

You can also relax the rule, allowing passwords that have been pwned multiple times.
In the example below, passwords that have been pwned between 0 and 4 times are allowed:

```php
$request->validate([
    'password' => 'required|string|min:6|confirmed|pwned:5',
]);
```

Of course, you can also use a Rule object instead:

```php
use Ubient\PwnedPasswords\Rules\Pwned;

$request->validate([
    'password' => ['required', 'string', 'min:6', 'confirmed', new Pwned(5)],
]);
```

#### Handling Lookup Errors
When the Pwned Passwords API cannot be queried, the default behavior is to accept the password as non-pwned and to send a warning message to the log.
While this doesn't add much value, it does allow you to be aware of when a pwned password was allowed, and to potentially manually act on this.

If you would like to automatically do something based on this lookup error (such as marking the request as potentially pwned), or want to decline the password instead,
you may create your own implementation of the [LookupErrorHandler](src/Contracts/LookupErrorHandler.php) and overwrite the default binding in your application:

```php
use Ubient\PwnedPasswords\Contracts\LookupErrorHandler;

$this->app->bind(LookupErrorHandler::class, MyCustomErrorHandler::class);
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email claudio@ubient.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

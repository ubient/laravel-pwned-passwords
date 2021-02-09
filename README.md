![Pwned Passwords](https://banners.beyondco.de/Laravel%20Pwned%20Passwords.png?theme=light&packageName=ubient%2Flaravel-pwned-passwords&pattern=brickWall&style=style_1&description=Automatically+check+password+safety+against+existing+data+breaches&md=1&fontSize=100px&images=lock-closed&widths=auto&heights=250)

<p align="center">
  <a href="https://github.com/ubient/laravel-pwned-passwords/releases">
    <img src="https://img.shields.io/github/release/ubient/laravel-pwned-passwords.svg?style=flat-square" alt="Latest Version">
  </a>
  <a href="https://github.com/ubient/laravel-pwned-passwords/actions?query=workflow%3Atests+branch%3Amaster">
    <img src="https://img.shields.io/github/workflow/status/ubient/laravel-pwned-passwords/tests/master.svg?style=flat-square" alt="Build Status">
  </a>
  <a href="https://scrutinizer-ci.com/g/ubient/laravel-pwned-passwords">
    <img src="https://img.shields.io/scrutinizer/g/ubient/laravel-pwned-passwords.svg?style=flat-square" alt="Quality Score">
  </a>
  <a href="https://styleci.io/repos/151966705"><img src="https://styleci.io/repos/151966705/shield" alt="StyleCI"></a>
  <a href="https://packagist.org/packages/ubient/laravel-pwned-passwords">
    <img src="https://img.shields.io/packagist/dt/ubient/laravel-pwned-passwords.svg?style=flat-square" alt="Total Downloads">
  </a>
</p>

# Pwned Passwords

This package provides a validation rule that allows you to prevent or limit the re-use of passwords that are known to be unsafe for ongoing usage. 
The result is a more secure application, as your users will have a much lower risk of having their accounts taken over.

### How it works

Internally, the validation rule uses what is known as a [k-Anonymity model](https://en.wikipedia.org/wiki/K-anonymity) that allows for the password to be looked up without giving up the user's privacy or security:

- First, we hash the password using SHA-1
- Next, it looks up the first 5 characters of this hash against TroyHunt's [Pwned Passwords (haveibeenpwned.com)](https://haveibeenpwned.com/Passwords) API. 
- The API then responds with a list _suffixes_ to these first 5 characters that we are looking up.
- Finally, we search through the list, checking whether the suffix of our hashed password matches any of the entries.

This will then tell us whether a password was breached, and if so, how frequent.

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

It is also possible to relax the rule, allowing passwords that have been breached multiple times.
In the following example, passwords that have been pwned between 0 and 4 times are allowed:

```php
$request->validate([
    'password' => 'required|string|min:6|confirmed|pwned:5',
]);
```

Alternatively, you can also achieve the same using a Rule object:

```php
use Ubient\PwnedPasswords\Rules\Pwned;

$request->validate([
    'password' => ['required', 'string', 'min:6', 'confirmed', new Pwned(5)],
]);
```

#### Handling Lookup Errors
When the [Pwned Passwords](https://haveibeenpwned.com/Passwords) API cannot be queried, the default behavior is to accept the password as non-pwned and to send a warning message to the log. While this by itself doesn't add much value, it does allow you to be aware of when a pwned password was allowed, and to potentially manually act on this.

If you would like to automatically do something else based on this lookup error (such as marking the request as potentially pwned), or want to decline the password instead,
you may create your own implementation of the [LookupErrorHandler](src/Contracts/LookupErrorHandler.php) and overwrite the default binding in your application:

```php
use Ubient\PwnedPasswords\Contracts\LookupErrorHandler;

$this->app->bind(LookupErrorHandler::class, MyCustomErrorHandler::class);
```

#### Overriding localization
If following the [Laravel Docs](https://laravel.com/docs/master/localization#overriding-package-language-files) to override the validation message provided by this package you'll need to use the `PwnedPasswords` namespace for the file path.

To override the validation message provided by this package, place your file at `resources/lang/vendor/PwnedPasswords/en/validation.php`.

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

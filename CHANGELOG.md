# Changelog

All notable changes to `ubient/laravel-pwned-passwords` will be documented in this file

## 2.0.1 - 2020-04-11
- Add support for Laravel 7
- Fixed a bug where an error might be thrown for not being able to reach the Pwned Passwords API. 
  Instead, the default behaviour now is to accept the password as non-pwned and send a warning to Laravel's Log.
  If you would like to override this behaviour, you can [create your own implementation of the LookupErrorHandler and bind it in your application](README.md#handling-lookup-errors).

## 2.0.0 - 2019-09-03
- Drop support for Laravel 5.7 and older
- Add support for Laravel 6

## 1.1.0 - 2019-02-27
- Drop support for PHP 7.1
- Add support for Laravel 5.8

## 1.0.0 - 2018-10-08
- Initial release

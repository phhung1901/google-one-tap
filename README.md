[![Latest Version on Packagist](https://img.shields.io/packagist/v/phhung1901/google_one_tap.svg?style=flat-square)](https://packagist.org/packages/phhung1901/google_one_tap)
[![Total Downloads](https://img.shields.io/packagist/dt/phhung1901/google_one_tap.svg?style=flat-square)](https://packagist.org/packages/phhung1901/google_one_tap)

# google-one-tap
Login with google one tap/google popup login for Laravel
![title](https://developers.google.com/static/identity/gsi/web/images/one-tap-sign-up.png?hl=vi)

---
## Installation
**requires**
- php: >=8.1
- laravel/framework: ^9.0 || ^10.0

```bash
composer require phhung1901/google_one_tap
```
---
## Config
### Add configuration to `config/services.php`

```php
'google' => [
  'client_id' => env('GOOGLE_CLIENT_ID'),
  'client_secret' => env('GOOGLE_CLIENT_SECRET'),
  'redirect' => env('GOOGLE_URL')
],
```
---

## Usage
### 1. Added to scripts
```html
<script src="https://accounts.google.com/gsi/client" async="" defer=""></script>
```
### 2. Next, you must publish the component
Add to `providers` config/app.php
```php
\GoogleOneTap\Services\GoogleOneTapServiceProvider::class,
```

```bash
php artisan vendor:publish --tag=google_one_tap
```
Now, add the component wherever you want google_one_tap to be used.
```html
<x-google_one_tap.onload/>
```
Config `googe-one-tap.php` you can add customizations

### 3. Returned User fields
```php
$token = GoogleOneTapService::getToken($request);
return Socialite::driver('google-one-tap')->stateless()->userFromToken($token)
```

---
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

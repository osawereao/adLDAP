# adLDAP

adLDAP is a PHP class library that provides LDAP authentication and Active Directory management tools, modernized for **PHP 8.2+**.

---

## Requirements

- **PHP**: `^8.2` (tested with PHP 8.2.35 and PHP 8.5)
- **PHP LDAP Extension**: `ext-ldap` enabled in your `php.ini`
- **OpenSSL Extension** *(optional)*: Required if connecting to Active Directory over LDAPS / SSL (`use_ssl => true`) or TLS.

---

## Installation

### Option 1: Install via Composer (Recommended)

Once published to Packagist, install the package via your terminal:

```bash
composer require osawereao/adldap
```

Or add it directly to your project's `composer.json`:

```json
"require": {
    "osawereao/adldap": "^5.1"
}
```

Then update your dependencies:

```bash
composer update osawereao/adldap
```

---

### Option 2: Local / Path Repository (Before Packagist Release)

If you are developing locally or want to use this package in your project before publishing to Packagist, add a path repository to your application's `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "../adldap"
    }
],
"require": {
    "osawereao/adldap": "*"
}
```

Then install:

```bash
composer update osawereao/adldap
```

---

### Option 3: Direct Git Repository

To pull directly from your GitHub repository:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/osawereao/adldap.git"
    }
],
"require": {
    "osawereao/adldap": "dev-main"
}
```

Then install:

```bash
composer update osawereao/adldap
```

---

## Basic Usage

### 1. Initializing and Connecting

```php
use Adldap\Adldap;

$config = [
    'account_suffix'     => '@domain.local',
    'domain_controllers' => ['dc1.domain.local', 'dc2.domain.local'],
    'base_dn'            => 'dc=domain,dc=local',
    'admin_username'     => 'administrator',
    'admin_password'     => 'SecretPassword',
    'ad_port'            => 389, // or 636 if use_ssl is true
    'use_ssl'            => false,
    'use_tls'            => false,
    'recursive_groups'   => true,
];

// Initialize and auto-connect
$ad = new Adldap($config);
```

### 2. User Authentication

Authenticate credentials against Active Directory:

```php
if ($ad->authenticate('jdoe', 'UserPassword')) {
    echo "Authentication successful!";
} else {
    echo "Invalid username or password.";
}
```

### 3. Finding and Querying Users

```php
// Find user by username, email, or display name (ANR search)
$user = $ad->user()->find('jdoe');

// Query with specific attributes
$user = $ad->user()->find('jdoe', ['cn', 'mail', 'telephonenumber', 'department']);

// Get all users
$allUsers = $ad->user()->all();
```

### 4. Search and Query Builder

```php
$results = $ad->search()
    ->select(['cn', 'mail', 'samaccountname'])
    ->where('department', '=', 'Engineering')
    ->get();
```

### 5. Using inside Laravel 9 / 10 / 11

In your `app/Providers/AppServiceProvider.php`:

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Adldap\Adldap;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Adldap::class, function ($app) {
            return new Adldap(config('adldap'));
        });
    }
}
```

Create a `config/adldap.php` config file:

```php
return [
    'account_suffix'     => env('ADLDAP_ACCOUNT_SUFFIX', '@domain.local'),
    'domain_controllers' => explode(',', env('ADLDAP_CONTROLLERS', 'dc1.domain.local')),
    'base_dn'            => env('ADLDAP_BASE_DN', 'dc=domain,dc=local'),
    'admin_username'     => env('ADLDAP_ADMIN_USER', 'administrator'),
    'admin_password'     => env('ADLDAP_ADMIN_PASS', 'SecretPassword'),
    'ad_port'            => (int) env('ADLDAP_PORT', 389),
    'use_ssl'            => env('ADLDAP_USE_SSL', false),
    'use_tls'            => env('ADLDAP_USE_TLS', false),
    'recursive_groups'   => true,
];
```

Then resolve anywhere in your controllers or services:

```php
$ad = app(Adldap::class);

if ($ad->authenticate($request->username, $request->password)) {
    // Authenticated
}
```

---

## Running Tests

This package includes a Pest / PHPUnit test suite:

```bash
# Run with Pest
./vendor/bin/pest

# Or run with PHPUnit
./vendor/bin/phpunit
```

To quickly verify PHP 8.2 compatibility without vendor binaries:

```bash
php test_cli.php
```

---

## Documentation

Full documentation and usage guides:
- [Search Functions](docs/SEARCH-FUNCTIONS.md)
- [User Functions](docs/USER-FUNCTIONS.md)
- [Group Functions](docs/GROUP-FUNCTIONS.md)
- [Folder Functions](docs/FOLDER-FUNCTIONS.md)
- [Exchange Functions](docs/EXCHANGE-FUNCTIONS.md)
- [Computer Functions](docs/COMPUTER-FUNCTIONS.md)
- [Contact Functions](docs/CONTACT-FUNCTIONS.md)

---

## Authors & Maintainers

- **Anthony Osawere** ([anthony@osawere.com](mailto:anthony@osawere.com)) - Maintainer
- **Richard Hyland** - Original Developer
- **Steve Bauman** - Contributor
- **Scott Barnett** - Contributor

---

## License

This library is licensed under the LGPL-2.1 License.

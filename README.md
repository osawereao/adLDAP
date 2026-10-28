# adLDAP

adLDAP is a PHP class library that provides LDAP authentication and Active Directory management tools, modernized for **PHP 8.2+** with native **Laravel 9+** package auto-discovery.

---

## Requirements

- **PHP**: `^8.2` (tested with PHP 8.2.35 and PHP 8.5)
- **PHP LDAP Extension**: `ext-ldap` enabled in your `php.ini`
- **OpenSSL Extension** *(optional)*: Required if connecting to Active Directory over LDAPS / SSL (`use_ssl => true`) or TLS.

---

## Installation & Setup

### 1. In Laravel (Laravel 9 / 10 / 11)

#### Step A: Install via Composer
```bash
composer require osawereao/adldap
```

#### Step B: Publish the Configuration
Laravel auto-discovers the service provider. Run the artisan command to publish `config/adldap.php`:

```bash
php artisan vendor:publish --tag="adldap-config"
```

#### Step C: Add your Environment Variables
Open your `.env` file and configure your Active Directory settings:

```env
ADLDAP_ACCOUNT_SUFFIX=@corp.local
ADLDAP_CONTROLLERS=dc1.corp.local,dc2.corp.local
ADLDAP_BASE_DN=dc=corp,dc=local
ADLDAP_ADMIN_USER=administrator
ADLDAP_ADMIN_PASS=SecretPassword
ADLDAP_PORT=389
ADLDAP_USE_SSL=false
ADLDAP_USE_TLS=false
```

That's it! The package is ready to use in your controllers and services.

---

### 2. Local / Development Setup (Before Packagist Release)

If testing locally in your application before the Packagist release:

1. Add a path repository to your project's `composer.json`:
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
2. Update dependencies:
   ```bash
   composer update osawereao/adldap
   ```
3. Publish configuration:
   ```bash
   php artisan vendor:publish --tag="adldap-config"
   ```

---

### 3. Standalone PHP Usage (Non-Laravel)

```bash
composer require osawereao/adldap
```

```php
use Adldap\Adldap;

$config = [
    'account_suffix'     => '@corp.local',
    'domain_controllers' => ['dc1.corp.local'],
    'base_dn'            => 'dc=corp,dc=local',
    'admin_username'     => 'administrator',
    'admin_password'     => 'SecretPassword',
    'ad_port'            => 389,
    'use_ssl'            => false,
    'use_tls'            => false,
];

$ad = new Adldap($config);
```

---

## Usage in Laravel

### Using the Facade
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Adldap::authenticate($credentials['username'], $credentials['password'])) {
            // Retrieve user details from Active Directory
            $adUser = Adldap::user()->find($credentials['username']);

            return response()->json([
                'status'  => 'success',
                'message' => 'Authenticated successfully',
                'user'    => [
                    'name'  => $adUser['displayname'] ?? null,
                    'email' => $adUser['mail'] ?? null,
                ]
            ]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
```

### Using Dependency Injection
```php
use Adldap\Adldap;

public function search(Request $request, Adldap $ad)
{
    $users = $ad->search()
        ->select(['cn', 'mail', 'department'])
        ->where('department', '=', 'Engineering')
        ->get();

    return response()->json($users);
}
```

---

## Running Tests

Run the test suite via Pest:

```bash
./vendor/bin/pest
```

Or run standalone CLI diagnostics without Composer binaries:

```bash
php test_cli.php
```

---

## Authors & Maintainers

- **Anthony Osawere** ([anthony@osawere.com](mailto:anthony@osawere.com)) - Maintainer
- **Richard Hyland** - Original Developer
- **Steve Bauman** - Contributor
- **Scott Barnett** - Contributor

---

## License

This library is licensed under the LGPL-2.1 License.

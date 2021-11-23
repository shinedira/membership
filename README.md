# Membership

## Instalation 
### Install Package Via Composer
```
composer require timedoor/tmd-membership
```

Install Membership
```
php artisan membership:install
```
Publish config & migration
```
php artisan vendor:publish --tag=tmd-membership
```
- this will copy config `tmd-membership` and migration files


After that you can run 
```
php artisan migrate
```

After migration complete you have to add this section to `composer.json`
```json
"files": [
        "app/Helpers/membership.php"
    ]
```
- you can see detail of code here
- ```json
    "autoload-dev": {
            "psr-4": {
                "Tests\\": "tests/"
            },
            "files": [
                "app/Helpers/membership.php"
            ]
        },
    ```

Reload autoload
```
composer dump-autoload
```

Add route to `RouteServiceProvider`
```php
Route::prefix('api')
        ->middleware('api')
        ->namespace($this->namespace)
        ->group(base_path('routes/membership.php'));

    Route::middleware('web')
        ->prefix('admin')
        ->as('admin.')
        ->namespace($this->namespace)
        ->group(base_path('routes/admin.php'));
```
Add Fcm trait to your user models
```php
Timedoor\TmdMembership\traits\Fcmable;

use Fcmable;
```

>## Membership Ready To Use
<br />

## Admin Url
```
{{url}}/admin/dashboard
```
## API Url
| Module | Url | Method |
| --- | --- | --- |
| Register | `{{url}}/register` | GET |
| Login | `{{url}}/login` | GET |
| Profile | `{{url}}/user` | GET |
| OTP | --- | --- |
| Request | `{{url}}/otp/request` | POST |
| Check | `{{url}}/otp/check` | POST |

## You can get current FCM token
```php
$request->user()->currentFcmToken()->token
```
<br />
<br />

# Thank You

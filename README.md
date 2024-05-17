# Laravel smsapi notification channel (tutorial)

- v3.0 Catch exceptions to logs (SendSmssBag class from SmsApi)
- v2.0 Catch exceptions to logs (SmsMessage class)
- v1.0 Throws exception when SMS smsapi error

## Install

```sh
composer require smsapi/php-client
```

### Add route

```php
<?php

use App\Models\User;
use App\Notifications\OrderSms;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

Route::get('/sms', function () {

    try {
        Notification::sendNow(
            User::first(),
            new OrderSms('New Order [%idzdo:smsapi.pl/panel%]', ['48100100100'])
        );
    } catch (\Exception $e) {
        // Resend from another channel if error
        return $e->getMessage();
    }

    return 'Sms has been send.';

});
```

### Config SmsApi

config/sms.php

```php
<?php

return [
    'api_token' => 'EMPTY_API_TOKEN', // Api bearer token
    'api_from' => 'Test', // Default sms sender name
    'api_encoding' => 'utf-8',  // Charset
    'api_details' => true,  // Test mode
    'api_test' => false,  // Test mode
];
```

### Run server /sms url

```sh
php artisan serve --host=localhost
php artisan serve --host=localhost --port=8080
```

## Dev

Optional part for the SmsMessage class (not used in this version).

### Service Provider

Add SmsChannelProvider in bootstrap/providers.php if you want to use "sms" and not SmsChannel::class in the notification via() method.

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Channels\Sms\SmsChannelProvider::class,
];
```

### Update User model (optional)

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ...

    // Overwrite notification message user id
    public function routeNotificationForSms($notifiable)
    {
        return 'user-' . $this->id;
    }
}
```

### Notifications list

```php
<?php

return $user->notifications()->get();

return $user->notifications()->latest()->limit(5)->get();

return $user->notifications()->latest()
    ->offset($offset)->limit($perpage)
    ->get()->each(function ($n) {
        $n->formatted_created_at = $n->created_at->format('Y-m-d H:i:s');
    });

return $user->notifications()
    ->where('type', 'sms-channel')
    ->orderBy('created_at', 'desc')
    ->limit(5)->get();
```

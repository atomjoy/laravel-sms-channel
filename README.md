# Laravel smsapi channel

- v1.0 Throws exception when SMS smsapi error
- v2.0 Forwards the SMS message to the database

## Install

```sh
composer require smsapi/php-client
```

## Update User model

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

## Add route

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
            new OrderSms('New Order [%idzdo:smsapi.pl/panel%]', '48100100100')
        );
    } catch (\Exception $e) {
        // Resend from another channel if error
        return $e->getMessage();
    }

    return 'Sms has been send.';

});
```

## Service Provider (optional)

Add SmsChannelProvider in bootstrap/providers.php if you want to use "sms" and not SmsChannel::class in the notification via() method.

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Channels\Sms\SmsChannelProvider::class,
];
```

## Notifications list

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

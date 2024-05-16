# Laravel smsapi channel

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

    // Set database notifications channel type name
    public function routeNotificationForSms($notifiable)
    {
        return 'sms-channel-' . $this->id;
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

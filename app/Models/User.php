<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;

class User extends Authenticatable
{
	use HasFactory, Notifiable;

	// Set database notifications type
	public function routeNotificationForSms($notifiable)
	{
		return 'sms-channel-user-' . $this->id;
	}
}

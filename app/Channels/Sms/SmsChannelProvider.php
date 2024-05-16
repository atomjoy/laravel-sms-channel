<?php

namespace App\Channels\Sms;

use App\Channels\Sms\SmsChannel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

//
/**
 * Sms channel provider class.
 *
 * Add SmsChannelProvider in bootstrap/providers.php
 * if you want to use "sms" and not SmsChannel::class
 * in the notification via() method (optional).
 */
class SmsChannelProvider extends ServiceProvider
{
	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		Notification::extend('sms', function ($app) {
			return new SmsChannel();
		});
	}
}

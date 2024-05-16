<?php

namespace App\Channels\Sms;

use App\Channels\Sms\SmsChannel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

//
/**
 * Sms channel provider class
 *
 * (optional) Add in bootstrap/providers.php if you want use 'sms' nor SmsChannel::class in via() method.
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

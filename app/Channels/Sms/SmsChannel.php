<?php

namespace App\Channels\Sms;

use Illuminate\Notifications\Notification;
use App\Channels\Sms\SmsMessage;

// Install
// composer require smsapi/php-client
class SmsChannel
{
	/**
	 * Undocumented function
	 *
	 * @param \App\Models\User $notifiable
	 * @param \Illuminate\Notifications\Notification $notification
	 * @return mixed True on success or void on error
	 */
	public function send($notifiable, Notification $notification)
	{
		if (method_exists($notifiable, 'routeNotificationForSms')) {
			$id = $notifiable->routeNotificationForSms($notifiable);
		} else {
			$id = $notifiable->getKey();
		}

		$message = method_exists($notification, 'toSms')
			? $notification->toSms($notifiable)
			: '';

		if (!$message instanceof SmsMessage) {
			return; // Send from another channel
		}

		$message->from($id)->send();

		return true;
	}
}

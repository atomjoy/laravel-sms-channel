<?php

namespace App\Notifications;

use App\Channels\Sms\SmsChannel;
use App\Channels\Sms\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderSms extends Notification
{
	use Queueable;

	public function __construct(
		public string $message = '',
		public $mobile = null,
	) {
		$this->afterCommit();
	}

	/**
	 * Get the notification's database type.
	 *
	 * @return string
	 */
	public function databaseType(object $notifiable): string
	{
		return 'sms-channel';
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @return array<int, string>
	 */
	public function via($notifiable)
	{
		return [SmsChannel::class, 'database'];
	}

	/**
	 * Get the sms message representation of the notification.
	 *
	 * @return App\Channels\Sms\SmsMessage
	 */
	public function toSms($notifiable): SmsMessage
	{
		$mobile = $this->mobile ?? $notifiable?->mobile ?? '';

		return (new SmsMessage($mobile, $this->message));
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray($notifiable)
	{
		$mobile = $this->mobile ?? $notifiable?->mobile ?? '';

		return [
			'from' => 'sms-channel-' . $notifiable->id,
			'sms_number' => $mobile,
			'sms_message' => $this->message,
		];
	}
}

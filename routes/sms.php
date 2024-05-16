<?php

use App\Models\User;
use App\Notifications\OrderSms;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

// Main page
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

	return 'Message has been send.';
});

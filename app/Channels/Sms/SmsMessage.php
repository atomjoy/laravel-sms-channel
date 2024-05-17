<?php

namespace App\Channels\Sms;

use Illuminate\Support\Facades\Log;
use Smsapi\Client\Curl\SmsapiHttpClient;
use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;

/**
 * SmsMessage class
 *
 * Custom sms message class for sending.
 */
class SmsMessage
{
	protected $to;
	protected $message;
	protected $from = null;
	protected $api_token;
	protected $api_from;
	protected bool $api_test = false;

	public function __construct($mobile, $message)
	{
		$this->api_token = config('sms.api_token', '');
		$this->api_from = config('sms.api_from', 'Test');
		$this->api_test = (bool) config('sms.api_test', false);

		$this->to($mobile);
		$this->message($message);

		if (empty($this->api_token)) {
			throw new \Exception("Config sms api token (sms.api_token).");
		}

		if (empty($this->to) || empty($this->message)) {
			throw new \Exception("SMS Invalid message or number.");
		}
	}

	public function from($id): self
	{
		$this->from = $id;

		return $this;
	}

	public function to($mobile): self
	{
		$this->to = str_replace('+)(-', '', $mobile);
		return $this;
	}

	public function message($str): self
	{
		$this->message = $str;
		return $this;
	}

	/**
	 * Send sms message with smsapi or log to file.
	 *
	 * https://www.smsapi.pl/sms-api
	 * https://www.smsapi.pl/docs/?shell#wiadomosci-z-idz-do
	 * https://www.smsapi.pl/blog/podstawy/api-smsapi-od-podstaw-poradnik
	 *
	 * SmsApi response
	 * {"count":1,"list":[{"id":"566275XXXXX","points":0.14,"number":"48XXXXXXXXX","date_sent":1603356627,"submitted_number":"XXXXXXXXX","status":"QUEUE","error":null,"idx":null,"parts":1}]}
	 *
	 * @return void|bool
	 */
	public function send()
	{
		$sms = SendSmsBag::withMessage($this->to, $this->message);
		$sms->encoding = 'utf-8';

		if ($this->api_test) {
			$sms->test = 1;
		}

		try {
			$res = (new SmsapiHttpClient())
				->smsapiPlService($this->api_token)
				->smsFeature()
				->sendSms($sms);

			$this->log($res);
		} catch (\Exception $e) {
			report($e);
			$this->log($sms, 'SmsSendError');
		}
	}

	/**
	 * Log to file.
	 */
	function log($data, $msg = 'SmsSent'): void
	{
		Log::build([
			'driver' => 'single',
			'path' => storage_path('logs/sms.log'),
		])->info($msg, array('sms' => $data));
	}
}

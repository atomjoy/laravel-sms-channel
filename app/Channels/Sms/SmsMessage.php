<?php

namespace App\Channels\Sms;

use Illuminate\Support\Facades\Log;
use Smsapi\Client\Curl\SmsapiHttpClient;
use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;

class SmsMessage
{
	protected $to;
	protected $message;
	protected $api_token;
	protected $api_from;
	protected $from = null;

	public function __construct($mobile, $message)
	{
		$this->api_token = config('sms.api_token', '');
		$this->api_from = config('sms.api_from', 'Test');

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
	public function send(): void
	{
		$sms = SendSmsBag::withMessage($this->to, $this->message);
		$sms->encoding = 'utf-8';

		$res = (new SmsapiHttpClient())
			->smsapiPlService($this->api_token)
			->smsFeature()
			->sendSms($sms);

		$this->log($res);
	}

	/**
	 * Log to file.
	 */
	function log($data, $msg = 'SmsBagSent'): void
	{
		Log::build([
			'driver' => 'single',
			'path' => storage_path('logs/sms.log'),
		])->info($msg, array('sms' => $data));
	}
}

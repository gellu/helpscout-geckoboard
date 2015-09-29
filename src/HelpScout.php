<?php

/**
 * Author: Grzesiek
 * Date: 24.09.2015 14:20
 */
class HelpScout
{

	const PERIOD_DAY 	= 'day';
	const PERIOD_WEEK 	= 'week';
	const PERIOD_MONTH 	= 'month';

	private $apiKey;
	private $password;

	private $requestParams = [];
	/** @var \GuzzleHttp\Client  */
	private $client;

	public function __construct($apiKey)
	{
		$this->apiKey = $apiKey;
		$this->password = 'X';

		$this->requestParams = $requestParams = ['auth' => [$this->apiKey, $this->password]];

		$this->client = new \GuzzleHttp\Client();
	}

	public function getPagesCount($mailboxId, $folderId)
	{

		$res = $this->client->request('GET',
			'https://api.helpscout.net/v1/mailboxes/'. $mailboxId .'/folders/'. $folderId.'/conversations.json', $this->requestParams);

		$mailbox = json_decode($res->getBody()->getContents(), true);

		return $mailbox['pages'];
	}

	public function getAllActivePagesCount($mailboxId)
	{

		$res = $this->client->request('GET',
			'https://api.helpscout.net/v1/mailboxes/'. $mailboxId .'/conversations.json?status=active', $this->requestParams);

		$mailbox = json_decode($res->getBody()->getContents(), true);

		return $mailbox['pages'];
	}

	public function getConversations($mailboxId, $folderId, $page)
	{
		$res = $this->client->request('GET',
			'https://api.helpscout.net/v1/mailboxes/'. $mailboxId .'/folders/'. $folderId.'/conversations.json?page='. $page,
			$this->requestParams);
		$mailbox = json_decode($res->getBody()->getContents(), true);

		return $mailbox['items'];
	}

	public function getAllActiveConversations($mailboxId, $page)
	{
		$res = $this->client->request('GET',
			'https://api.helpscout.net/v1/mailboxes/'. $mailboxId .'/conversations.json?status=active&page='. $page,
			$this->requestParams);
		$mailbox = json_decode($res->getBody()->getContents(), true);

		return $mailbox['items'];
	}

	public function getHappinessRating($mailboxId, $period)
	{
		$startDate = new DateTime();

		switch($period)
		{
			case HelpScout::PERIOD_DAY:
				$startDate->modify('-1 day');
				break;
			case HelpScout::PERIOD_WEEK:
				$startDate->modify('-1 week');
				break;
			case HelpScout::PERIOD_MONTH:
				$startDate->modify('-1 month');
				break;
			default:
				$startDate->sub(new DateInterval('P1D'));
				break;
		}

		$start = $startDate->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
		$end = (new DateTime())->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');

		$res = $this->client->request('GET', 'https://api.helpscout.net/v1/reports/happiness.json?start='. $start . '&end='. $end .'&mailboxes='. $mailboxId, $this->requestParams);
		$report = json_decode($res->getBody()->getContents(), true);

		return ['happiness' => $report['current']['happinessScore'],
				'count' 	=> $report['current']['ratingsCount']];
	}
}
<?php

/**
 * Author: Grzesiek
 * Date: 24.09.2015 14:20
 */
class HelpScout
{

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
}
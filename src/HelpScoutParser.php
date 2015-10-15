<?php

/**
 * Author: Grzesiek
 * Date: 24.09.2015 14:18
 */
class HelpScoutParser
{

	protected $config = [];
	/** @var  HelpScout */
	protected $helpScout;

	protected $levels = [];
	private $levelCounter = ['ok' => 0, 'warn' => 0, 'critical' => 0];
	private $conversations = [];


	public function __construct($config, HelpScout $helpScout)
	{
		$this->config = $config;
		$this->helpScout = $helpScout;
		$this->recalculateLevels();
	}

	private function recalculateLevels ()
	{
		foreach($this->config['levels'] as $k => $v)
		{
			$this->levels[$k] = $v*60*60;
		}
	}

	public function parseLevels()
	{
		$pages = $this->helpScout->getPagesCount($this->config['mailboxId'], $this->config['folderId']);

		$currentPage = 1;

		$this->conversations = [];

		while ($currentPage <= $pages)
		{
			$conversations = $this->helpScout->getConversations($this->config['mailboxId'], $this->config['folderId'], $currentPage);

			foreach($conversations as $conversation)
			{
				$age = time() - strtotime($conversation[$this->config['age_field']]);

				if ($age < $this->levels['warn'])
				{
					$this->levelCounter['ok']++;
					$this->conversations['ok'][] = $conversation;
				}
				elseif ($age >= $this->levels['warn'] && $age < $this->levels['critical'] )
				{
					$this->levelCounter['warn']++;
					$this->conversations['warn'][] = $conversation;
				}
				elseif ($age >= $this->levels['critical'])
				{
					$this->levelCounter['critical']++;
					$this->conversations['critical'][] = $conversation;
				}
			}

			$currentPage++;
		}
	}

	public function parseLevelsUnassigned()
	{
		$pages = $this->helpScout->getAllActivePagesCount($this->config['mailboxId']);

		$currentPage = 1;

		$this->conversations = [];

		while ($currentPage <= $pages)
		{
			$conversations = $this->helpScout->getAllActiveConversations($this->config['mailboxId'], $currentPage);

			foreach($conversations as $conversation)
			{
				if($conversation['threadCount'] == 1)
				{
					$age = time() - strtotime($conversation[$this->config['age_field']]);

					if ($age < $this->levels['warn'])
					{
						$this->levelCounter['ok']++;
						$this->conversations['ok'][] = $conversation;
					}
					elseif ($age >= $this->levels['warn'] && $age < $this->levels['critical'])
					{
						$this->levelCounter['warn']++;
						$this->conversations['warn'][] = $conversation;
					}
					elseif ($age >= $this->levels['critical'])
					{
						$this->levelCounter['critical']++;
						$this->conversations['critical'][] = $conversation;
					}
				}
			}

			$currentPage++;
		}
	}

	public function getLevelCounter()
	{
		return $this->levelCounter;
	}

	public function getConversationsNumbers()
	{
		$numbers = [];

		foreach($this->conversations as $level => $conversations)
		{
			foreach($conversations as $conversation)
			{
				$numbers[$level][] = $conversation['number'];
			}
		}

		return $numbers;
	}

}
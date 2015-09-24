<?php
/**
 * Author: Grzesiek
 * Date: 24.09.2015 14:58
 */

const HELPSCOUT_API_KEY = '';

$config = [
	'counter' => [
		'name' => [
			'mailboxId'	=> '',
			'folderId'	=> '',
			'age_field'	=> '', 			# createdAt || userModifiedAt
			'levels'	=> [
				'ok'		=> '',
				'warn'		=> '',
				'critical'	=> ''
			],
			'levelNames' => [			# level names to show as descriptions on RAG widget
				'ok'		=> '',
				'warn'		=> '',
				'critical'	=> '',
			]
		]

	],

];
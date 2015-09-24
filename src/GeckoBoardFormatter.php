<?php

/**
 * Author: Grzesiek
 * Date: 24.09.2015 14:45
 */
class GeckoBoardFormatter
{
	public static function counterToRagFormatter($counter, $names)
	{
		return json_encode([
			'item' => [
				['value' => $counter['critical'], 'text' => $names['critical']],
				['value' => $counter['warn'], 'text' => $names['warn']],
				['value' => $counter['ok'], 'text' => $names['ok']],
			]
		]);

	}
}
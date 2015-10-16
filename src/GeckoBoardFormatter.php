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

	public static function happinessToNumberTextFormatter($data)
	{
		return json_encode([
			'item' => [
				['value' 	=> round($data['happiness'], 2),
				 'text' 	=> $data['countAll'] .' responses']
			]
		]);
	}

	public static function logToList($data)
	{
		$output = [];

		foreach($data as $row)
		{
			if($row['msg'] == 0)
			{
				$output[] = [
					'title' => ['text' => 'No tickets long past due'],
					'label'	=> ['name' => $row['date'], 'color' => '#90C564']
				];
			}
			else {
				$output[] = [
					'title' 		=> ['text' => $row['msg'] .' long past due'],
					'label'			=> ['name' => $row['date'], 'color' => '#E3524F'],
					'description'	=> 'tickets: '. $row['numbers'],
				];
			}

		}
		return json_encode($output);
	}
}
<?php
/**
 * Author: Grzesiek
 * Date: 15.10.2015 14:17
 */

class Logger
{

	const LOG_DIR = '../log/';
	const LOG_LIMIT  = 6;

	private $fileName;
	private $fileContents = [];

	public function __construct($filename)
	{
		$this->fileName = self::LOG_DIR . $filename;
		$this->getLogFile();
	}

	private function getLogFile()
	{
		$content = @file_get_contents($this->fileName);
		if($content)
		{
			$contentArr = explode("\n", $content);
			if(is_array($contentArr))
			{
				$labels = explode("\t", array_shift($contentArr));

				foreach($contentArr as $row)
				{
					$row = explode("\t", $row);
					$rowLabeled = [];
					foreach($row as $k => $v)
					{
						@$rowLabeled[$labels[$k]] = $v;
					}
					$this->fileContents[] = $rowLabeled;
				}
			}
		}
	}

	private function setLogFile()
	{
		$labels = [];

		if(count($this->fileContents) > self::LOG_LIMIT)
		{
			array_shift($this->fileContents);
		}

		foreach($this->fileContents[0] as $k => $v)
		{
			$labels[] = $k;
		}

		file_put_contents($this->fileName, implode("\t", $labels));

		foreach($this->fileContents as $rowArray)
		{
			file_put_contents($this->fileName, "\n" . implode("\t", $rowArray), FILE_APPEND);
		}
	}

	public function getLogFileContents()
	{
		return $this->fileContents;
	}

	public function log($msgArray)
	{
		$this->fileContents[] = $msgArray;
		$this->setLogFile();
	}

}
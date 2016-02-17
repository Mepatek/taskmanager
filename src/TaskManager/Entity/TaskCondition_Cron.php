<?php

namespace Mepatek\TaskManager\Entity;

use Nette\Utils\DateTime,
	Cron;

class TaskCondition_Cron extends TaskCondition
{

	/** @var string */
	protected $cronExpression;

	/**
	 * @return string
	 */
	public function getType()
	{
		return "Cron";
	}

	/**
	 * @return string
	 */
	public function getCronExpression()
	{
		return $this->cronExpression;
	}

	/**
	 * @param string $cronExpression
	 */
	public function setCronExpression($cronExpression)
	{
		$this->cronExpression = $cronExpression;
	}


	/**
	 * @return string
	 */
	public function getData()
	{
		$data = json_encode(
			array (
				"CronExpression" => $this->cronExpression,
			)
		);
		return $data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$decodedData = json_decode( $data );

		if ($decodedData) {
			$this->cronExpression = $decodedData->CronExpression;
		}

	}


	/**
	 * Get next run time
	 * @param DateTime $lastRun
	 * @return DateTime;
	 */
	public function getNextRunTime(DateTime $lastRun)
	{
		$cron = Cron\CronExpression::factory( $this->cronExpression );
		$nextRun = new DateTime;
		return $nextRun->from( $cron->getNextRunDate($lastRun) );
	}
}
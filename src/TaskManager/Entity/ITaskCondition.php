<?php

namespace Mepatek\TaskManager\Entity;

use Nette\Utils\DateTime;

interface ITaskCondition
{
	/**
	 * get next time run
	 * @parameter DateTime
	 * @return DateTime
	 */
	public function getNextRunTime( DateTime $lastRun );
	/** @return string */
	public function getData();
	/** @param string $data */
	public function setData($data);
	/** @return string */
	public function getType();
}
<?php

namespace Mepatek\TaskManager\Entity;

use Nette\Utils\DateTime;

/**
 * Interface ITaskCondition
 * @package Mepatek\TaskManager\Entity
 */
interface ITaskCondition
{
	/**
	 * get next time run
	 *
	 * @param DateTime $lastRun
	 *
	 * @return DateTime
	 */
	public function getNextRunTime(DateTime $lastRun);

	/** @return string */
	public function getData();

	/** @param string $data */
	public function setData($data);

	/** @return string */
	public function getType();
}
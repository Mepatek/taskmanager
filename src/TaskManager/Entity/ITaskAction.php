<?php
namespace Mepatek\TaskManager\Entity;

/**
 * Interface ITaskAction
 * @package Mepatek\TaskManager\Entity
 */
interface ITaskAction
{
	/**
	 * Run task
	 *
	 * @param \Nette\DI\Container $container
	 * @param string              $tasksDir
	 * @param string              $tasksDir
	 * @param Output              $output
	 *
	 * @return bool
	 */
	public function run($container, $tasksDir, Output $output);

	/** @return string */
	public function getData();

	/** @param string $data */
	public function setData($data);

	/** @return string */
	public function getType();

}
<?php
namespace Mepatek\TaskManager\Entity;

/**
 * Interface ITaskAction
 * @package Mepatek\TaskManager\Entity
 */
interface ITaskAction
{
	/** run task */
	public function run( $container, $tasksDir, \Mepatek\TaskManager\Entity\Output $output );
	/** @return string */
	public function getData();
	/** @param string $data */
	public function setData($data);
	/** @return string */
	public function getType();

}
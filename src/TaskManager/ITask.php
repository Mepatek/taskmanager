<?php

namespace Mepatek\TaskManager;

use Nette\DI\Container,
	Mepatek\TaskManager\Entity\Output;

/**
 * Interface ITask
 * @package Mepatek\TaskManager
 */
interface ITask
{
	/**
	 * set container and arguments
	 *
	 * @param Container                          $container
	 * @param \Mepatek\TaskManager\Entity\Output $output
	 * @param array                              $arguments
	 */
	public function __construct(Container $container, Output $output, array $arguments);

	/** run task */
	public function run();
}
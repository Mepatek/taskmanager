<?php

namespace Mepatek\TaskManager;

use Nette\DI\Container;

/**
 * Interface ITask
 * @package Mepatek\TaskManager
 */
interface ITask
{
	/**
	 * set container and arguments
	 * @param Container $container
	 * @param array $arguments
	 */
	public function __construct( Container $container, array $arguments );

	/** run task */
	public function run(  );
}
<?php

namespace Mepatek\TaskManager;


/**
 * Interface ITask
 * @package Mepatek\TaskManager
 */
interface ITask
{
	/** setup task and set container */
	public function setUp( $container );

	/** run task */
	public function run(  );
}
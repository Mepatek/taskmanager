<?php

namespace Mepatek\TaskManager;


use Nette\Database\Context;


class TaskLauncher
{

	/** @var string|null */
	public $taskDir = null;

	/** @var Context */
	private $database;

	/**
	 * TaskLauncher constructor.
	 * @param Context $database
	 */
	public function __construct( $database )
	{
		$this->database = $database;
	}

	/**
	 * Run all tasks in plan
	 */
	public function run( )
	{
		echo "XXX";
	}
}
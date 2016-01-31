<?php

namespace Mepatek\TaskManager;


use Nette\Database\Context,
	Mepatek\TaskManager\Repository\TaskRepository,
	Mepatek\TaskManager\Mapper\TaskNetteDatabaseMapper;


class TaskLauncher
{

	/** @var string */
	public $taskDir;

	/** @var Context */
	private $database;

	/** @var TaskRepository */
	private $taskRepository;

	/**
	 * TaskLauncher constructor.
	 * @param Context $database
	 * @param string $taskDir
	 */
	public function __construct( $database, $taskDir )
	{
		$this->database = $database;
		$this->taskDir = $taskDir;
		$taskMapper = new TaskNetteDatabaseMapper( $this->database );
		$this->taskRepository = new TaskRepository( $taskMapper );
	}

	/**
	 * Run all tasks in plan
	 */
	public function run( )
	{
		echo "XXX";
	}
}
<?php

namespace Mepatek\TaskManager;

use Exception;

use Nette\Database\Context,
	Mepatek\TaskManager\Repository\TaskRepository,
	Mepatek\TaskManager\Mapper\TaskNetteDatabaseMapper,
	Mepatek\TaskManager\Repository\TaskHistoryRepository,
	Mepatek\TaskManager\Mapper\TaskHistoryNetteDatabaseMapper,
	Mepatek\TaskManager\Entity\TaskHistory,
	Mepatek\TaskManager\Entity\Task,
	Nette\Utils\DateTime;


class TaskLauncher
{

	/** @var Context */
	private $database;
	/** @var \Nette\DI\Container */
	private $container;
	/** @var string */
	private $tasksDir;

	/** @var TaskRepository */
	private $taskRepository;
	/** @var TaskHistoryRepository */
	private $taskHistoryRepository;

	/**
	 * TaskLauncher constructor.
	 * @param Context $database
	 * @param string $container
	 * @param string $tasksDir
	 */
	public function __construct( $database, $container, $tasksDir )
	{
		$this->database = $database;
		$this->container = $container;
		$this->tasksDir = $tasksDir;

		$taskMapper = new TaskNetteDatabaseMapper( $this->database );
		$this->taskRepository = new TaskRepository( $taskMapper );
		$taskHistoryMapper = new TaskHistoryNetteDatabaseMapper( $this->database );
		$this->taskHistoryRepository = new TaskHistoryRepository( $taskHistoryMapper );
	}

	/**
	 * Run all tasks in plan
	 */
	public function run( )
	{

		/*
		$task = new \Mepatek\TaskManager\Entity\Task();
		$task->name = "Načítání přijatých dokladů";
		$task->source = "admin";
		$task->author = "Mepatek";
		$task->description = "Pravidelně prochází adresář se skeny a načítá dokumenty do Varia k přijatým dokladům";
		$this->taskRepository->save($task);
		*/

		$success = true;

		// find all the tasks to be run
		$tasks = $this->taskRepository->findTasksToRun();
		foreach ($tasks as $task) {

			// task history ..
			$taskHistory = new TaskHistory();
			$taskHistory->taskId = $task->id;
			$taskHistory->started = new DateTime();

			$resultCode = 0;
			$outputError = "";

			// buffering output
			ob_start();

			// if set state running run task
			try {
				$this->taskRepository->setStateRunning( $task );

				$success = $task->run( $this->container, $this->tasksDir )
					and $success;

			} catch (Exception $e) {
				$resultCode = $e->getCode();
				$outputError = $outputError . "ERROR (code: " . $e->getCode() . ") " . $e->getMessage() . "\n"
						. "file '" . $e->getFile() . "' (line " . $e->getLine() . ")\n"
						. $e->getTraceAsString() . "\n\n";
				$success = false;
			}


			$output = ob_get_contents();
			ob_end_clean();

			$taskHistory->finished = new DateTime();
			$taskHistory->resultCode = $resultCode;
			$taskHistory->output = ( $outputError ? $outputError : "" )
				. $output;
			$this->taskHistoryRepository->save( $taskHistory );

			// set state idle
			$task->setLastAndNextRun( );
			$task->state = 0;
			$this->taskRepository->save( $task );
		}
		//var_dump($tasks);

		return $success;
	}
}
<?php
/**
 * TEST: Test all providers with all mappers
 */

namespace Test;

use Mepatek\TaskManager\Entity\TaskAction_ITask;
use Mepatek\TaskManager\Entity\TaskCondition_Cron;
use Mepatek\TaskManager\Entity\TaskHistory;
use Nette,
	Tester,
	Tester\Assert;

require __DIR__ . '/bootstrap.php';


class RepositoryAndMapperTest extends Tester\TestCase
{
	const TRANSACTION = true;

	private $database;

	private $varchar20;
	private $varchar30;
	private $varchar40;
	private $varchar50;
	private $varchar60;
	private $varchar100;
	private $varchar150;
	private $varchar200;
	private $varchar250;
	private $varchar255;
	private $binaryData;
	private $text;
	private $datetime;
	private $float;

	function __construct()
	{
	}


	function setUp()
	{
		$connection = new \Nette\Database\Connection("sqlite:" . __DIR__ . "/data/TaskManager.db",
			null, null);
		$structure = new \Nette\Database\Structure( $connection, new \Nette\Caching\Storages\FileStorage(TEMP_DIR) );
		$conventions = new \Nette\Database\Conventions\DiscoveredConventions( $structure );
		$this->database = new \Nette\Database\Context($connection, $structure, $conventions);


		$this->varchar20 = (string)Nette\Utils\Random::generate(20);
		$this->varchar30 = (string)Nette\Utils\Random::generate(30);
		$this->varchar40 = (string)Nette\Utils\Random::generate(40);
		$this->varchar50 = (string)Nette\Utils\Random::generate(50);
		$this->varchar60 = (string)Nette\Utils\Random::generate(60);
		$this->varchar100 = (string)Nette\Utils\Random::generate(100);
		$this->varchar150 = (string)Nette\Utils\Random::generate(150);
		$this->varchar200 = (string)Nette\Utils\Random::generate(200);
		$this->varchar250 = (string)Nette\Utils\Random::generate(250);
		$this->varchar255 = (string)Nette\Utils\Random::generate(255);
		$this->text = (string)Nette\Utils\Random::generate(8000);
		$this->binaryData = (string)Nette\Utils\Random::generate(2048);
		$this->datetime = new \Nette\Utils\DateTime();
		$this->datetime1 = new \Nette\Utils\DateTime("2015-12-12");
		$this->date = new \Nette\Utils\DateTime("2015-12-10");
		$this->float = ((float)Nette\Utils\Random::generate(10, "0-9")) / 1000;
	}


	function testTaskRepositoryNetteDatabaseMapper()
	{
		$mapper = new \Mepatek\TaskManager\Mapper\TaskNetteDatabaseMapper($this->database);
		$repository = new \Mepatek\TaskManager\Repository\TaskRepository($mapper);

		$this->beginTransaction();

		$task = new \Mepatek\TaskManager\Entity\Task;

		$task->name = $this->varchar150;
		$task->created = $this->datetime;
		$task->source = $this->varchar255;
		$task->author= $this->varchar100;
		$task->description = $this->text;
		$task->deleteAfterRun = TRUE;
		$task->maxExecutionTimeInSecond = 88;
		$task->state = 0;
		$task->disabled = FALSE;
		$task->nextRun = $this->datetime;
		$task->lastRun = $this->datetime;
		$task->lastSuccess = TRUE;

		$action = new TaskAction_ITask;
		$action->order = 1;
		$action->className = $this->varchar50;
		$action->nameSpace =  $this->varchar100;
		$action->arguments = array();
		$task->addAction( $action );

		$condition = new TaskCondition_Cron;
		$condition->created = $this->datetime;
		$condition->expired = $this->datetime;
		$condition->active =  TRUE;
		$condition->cronExpression = $this->varchar50;
		$task->addCondition( $condition );

		Assert::true( $repository->save($task) );

		$itemId = $task->id;
		Assert::type("integer", $itemId);

		$item1 = $repository->find($itemId);
		Assert::type("Mepatek\\TaskManager\\Entity\\Task", $item1);
		Assert::equal($task, $item1);

		$repository->setStateRunning($task);
		$item1 = $repository->find($itemId);
		Assert::type("Mepatek\\TaskManager\\Entity\\Task", $item1);
		Assert::type("Nette\\Utils\\DateTime", $item1->exceedDateTime);
		Assert::equal($task, $item1);

		// update
		$item1->name = "1";
		$item1->source = "1";
		$item1->author= "1";
		$item1->description = "1";
		$item1->deleteAfterRun = FALSE;
		$item1->state = 0;
		$item1->nextRun = $this->datetime1;
		$item1->lastRun = $this->datetime1;
		$item1->lastSuccess = FALSE;
		Assert::true($repository->save($item1));

		// find
		$item2 = $repository->find($itemId);
		Assert::equal($item1, $item2);
		Assert::notEqual($task, $item2);

		// delete
		Assert::true($repository->deletePermanently($itemId));

		// find - NOT FOUND (NULL) is OK
		Assert::null($repository->find($itemId));

		$this->rollBack();
	}


	function testTaskHistoryRepositoryNetteDatabaseMapper()
	{
		$mapper = new \Mepatek\TaskManager\Mapper\TaskNetteDatabaseMapper($this->database);
		$repository = new \Mepatek\TaskManager\Repository\TaskRepository($mapper);

		$mapperHistory = new \Mepatek\TaskManager\Mapper\TaskHistoryNetteDatabaseMapper($this->database);
		$repositoryHistory = new \Mepatek\TaskManager\Repository\TaskHistoryRepository($mapperHistory);

		$this->beginTransaction();

		$task = new \Mepatek\TaskManager\Entity\Task;

		$task->name = $this->varchar150;
		$task->created = $this->datetime;
		$task->source = $this->varchar255;
		$task->author= $this->varchar100;
		$task->description = $this->text;
		$task->deleteAfterRun = TRUE;
		$task->state = 1;
		$task->disabled = FALSE;
		$task->nextRun = $this->datetime;
		$task->lastRun = $this->datetime;
		$task->lastSuccess = TRUE;

		Assert::true( $repository->save($task) );

		$taskHistory = new TaskHistory;
		$taskHistory->taskId = $task->id;
		$taskHistory->started = $this->datetime;
		$taskHistory->finished = $this->datetime;
		$taskHistory->resultCode = 0;
		$taskHistory->user = $this->varchar150;
		$output = new \Mepatek\TaskManager\Entity\Output();
		$output->write($this->text);
		$taskHistory->output = $output;

		Assert::true( $repositoryHistory->save($taskHistory) );

		$itemId = $taskHistory->id;
		Assert::type("integer", $itemId);

		$item1 = $repositoryHistory->find($itemId);
		Assert::type("Mepatek\\TaskManager\\Entity\\TaskHistory", $item1);
		Assert::equal($taskHistory, $item1);

		// update
		$item1->started = $this->datetime1;
		$item1->finished = $this->datetime;
		$item1->resultCode = 2;
		$item1->user = "1";
		$output->output = "1";
		$item1->output = $output->output;
		Assert::true($repositoryHistory->save($item1));

		// find
		$item2 = $repositoryHistory->find($itemId);
		Assert::equal($item1, $item2);
		Assert::notEqual($task, $item2);

		$repositoryHistory->fillTask($task);
		Assert::equal($task->history[0], $item2);

		// delete
		Assert::true($repositoryHistory->delete($itemId));

		// find - NOT FOUND (NULL) is OK
		Assert::null($repositoryHistory->find($itemId));

		$this->rollBack();
	}

	/**
	 * If TRANSACTION is set to TRUE, begin database transaction
	 */
	private function beginTransaction()
	{
		if (self::TRANSACTION) {
			$this->database->beginTransaction();
		}
	}

	/**
	 * If TRANSACTION is set to TRUE, rollBack
	 */
	private function rollBack()
	{
		if (self::TRANSACTION) {
			$this->database->rollBack();
		}
	}
}


$test = new RepositoryAndMapperTest();
$test->run();

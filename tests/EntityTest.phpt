<?php

namespace Test;

use Mepatek\TaskManager\Entity\Output;
use Nette,
	Tester,
	Tester\Assert;

require __DIR__ . '/bootstrap.php';

class EntityTest extends Tester\TestCase
{
	private $varchar20;
	private $varchar30;
	private $varchar40;
	private $varchar50;
	private $varchar60;
	private $varchar100;
	private $varchar150;
	private $varchar255;
	private $text;
	private $datetime;
	private $float;


	function __construct()
	{
	}


	function setUp()
	{
		$this->varchar20 = (string)Nette\Utils\Random::generate(20);
		$this->varchar30 = (string)Nette\Utils\Random::generate(30);
		$this->varchar40 = (string)Nette\Utils\Random::generate(40);
		$this->varchar50 = (string)Nette\Utils\Random::generate(50);
		$this->varchar60 = (string)Nette\Utils\Random::generate(60);
		$this->varchar100 = (string)Nette\Utils\Random::generate(100);
		$this->varchar150 = (string)Nette\Utils\Random::generate(150);
		$this->varchar255 = (string)Nette\Utils\Random::generate(255);
		$this->text = (string)Nette\Utils\Random::generate(8000);
		$this->datetime = new \Nette\Utils\DateTime();
		$this->float = ((float)Nette\Utils\Random::generate(10, "0-9")) / 1024;
	}


	function testTask()
	{

		$task = new \Mepatek\TaskManager\Entity\Task();

		$task->id = 10;
		$task->name = $this->varchar150;
		$task->created = $this->datetime;
		$task->source = $this->varchar255;
		$task->author= $this->varchar100;
		$task->description = $this->text;
		$task->deleteAfterRun = TRUE;
		$task->state = 1;
		$task->disabled = TRUE;
		$task->nextRun = $this->datetime;
		$task->lastRun = $this->datetime;
		$task->lastSuccess = TRUE;

		// tests ...
		Assert::same(10, $task->id);
		Assert::same($this->varchar150, $task->name);
		Assert::equal($this->datetime, $task->created);
		Assert::same($this->varchar255, $task->source);
		Assert::same($this->varchar100, $task->author);
		Assert::same($this->text, $task->description);
		Assert::true($task->deleteAfterRun);
		Assert::same(1, $task->state);
		Assert::true($task->disabled);
		Assert::equal($this->datetime, $task->nextRun);
		Assert::equal($this->datetime, $task->lastRun);
		Assert::true($task->lastSuccess);
	}

	function testTaskAction_ITask()
	{

		$taskAction_ITask = new \Mepatek\TaskManager\Entity\TaskAction_ITask();

		$taskAction_ITask->id = 12;
		$taskAction_ITask->order = 2;
		$taskAction_ITask->nameSpace = $this->varchar150;
		$taskAction_ITask->className = $this->varchar100;
		$taskAction_ITask->arguments = array();

		// tests ...
		Assert::same(12, $taskAction_ITask->id);
		Assert::same("ITask", $taskAction_ITask->type);
		Assert::same(2, $taskAction_ITask->order);
		Assert::same($this->varchar150, $taskAction_ITask->nameSpace);
		Assert::same($this->varchar100, $taskAction_ITask->className);
		Assert::equal(array(), $taskAction_ITask->arguments);
	}

	function testTaskCondition_Cron()
	{

		$taskCondition_Cron = new \Mepatek\TaskManager\Entity\TaskCondition_Cron();

		$taskCondition_Cron->id = 13;
		$taskCondition_Cron->created = $this->datetime;
		$taskCondition_Cron->expired = $this->datetime;
		$taskCondition_Cron->active = TRUE;
		$taskCondition_Cron->cronExpression = $this->varchar50;

		// tests ...
		Assert::same(13, $taskCondition_Cron->id);
		Assert::same("Cron", $taskCondition_Cron->type);
		Assert::equal($this->datetime, $taskCondition_Cron->created);
		Assert::equal($this->datetime, $taskCondition_Cron->expired);
		Assert::true($taskCondition_Cron->active);
		Assert::same($this->varchar50, $taskCondition_Cron->cronExpression);
	}

	function testTaskHistory()
	{

		$taskHistory = new \Mepatek\TaskManager\Entity\TaskHistory();

		$taskHistory->id = 8;
		$taskHistory->taskId = 9;
		$taskHistory->started = $this->datetime;
		$taskHistory->finished = $this->datetime;
		$taskHistory->resultCode = 0;
		$taskHistory->user = $this->varchar150;
		$output = new Output();
		$output->write($this->text);
		$taskHistory->output = $output;

		// tests ...
		Assert::same(8, $taskHistory->id);
		Assert::same(9, $taskHistory->taskId);
		Assert::equal($this->datetime, $taskHistory->started);
		Assert::equal($this->datetime, $taskHistory->finished);
		Assert::same(0, $taskHistory->resultCode);
		Assert::same($this->varchar150, $taskHistory->user);
		Assert::same($output, $taskHistory->output);
	}
}


$test = new EntityTest();
$test->run();

<?php


namespace Mepatek\TaskManager\Entity;

use Mepatek\Entity\AbstractEntity;
use Nette\Utils\DateTime;
use Exception;

/**
 * Class Task
 * @package Mepatek\TaskManager\Entity
 */
class Task extends AbstractEntity
{

	/** @var integer */
	protected $id = null;
	/** @var string 150 */
	protected $name;
	/** @var \Nette\Utils\DateTime */
	protected $created;
	/** @var string 255 */
	protected $source;
	/** @var string 100 */
	protected $author;
	/** @var string */
	protected $description;
	/** @var boolean */
	protected $deleteAfterRun = false;
	/** @var integer */
	protected $state = 0;
	/** @var boolean */
	protected $disabled = false;
	/** @var \Nette\Utils\DateTime */
	protected $nextRun;
	/** @var \Nette\Utils\DateTime */
	protected $lastRun;
	/** @var bool */
	protected $lastSuccess;

	/**
	 * @var TaskAction[]
	 * id => TaskAction
	 */
	private $actions = [];
	/**
	 * @var TaskCondition[]
	 * id => TaskCondition
	 */
	private $conditions = [];
	/**
	 * @var TaskHistory[]
	 * id => TaskHistory
	 * NULL -> not loaded, array -> loaded
	 */
	private $history = null;


	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		// ONLY if id is not set
		if (!$this->id) {
			$this->id = (int)$id;
		}
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $this->StringTruncate($name, 150);
	}

	/**
	 * @return \Nette\Utils\DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param \Nette\Utils\DateTime $created
	 */
	public function setCreated($created)
	{
		$this->created = $this->DateTime($created);
	}

	/**
	 * @return string
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * @param string $source
	 */
	public function setSource($source)
	{
		$this->source = $this->StringTruncate($source, 255);
	}

	/**
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param string $author
	 */
	public function setAuthor($author)
	{
		$this->author = $this->StringTruncate($author, 100);
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @return boolean
	 */
	public function getDeleteAfterRun()
	{
		return $this->deleteAfterRun ? true : false;
	}

	/**
	 * @param boolean $deleteAfterRun
	 */
	public function setDeleteAfterRun($deleteAfterRun)
	{
		$this->deleteAfterRun = $deleteAfterRun ? true : false;
	}

	/**
	 * @return int
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param int $state
	 */
	public function setState($state)
	{
		$this->state = (int)$state;
	}

	/**
	 * @return boolean
	 */
	public function getDisabled()
	{
		return $this->disabled ? true : false;
	}

	/**
	 * @param boolean $disabled
	 */
	public function setDisabled($disabled)
	{
		$this->disabled = $disabled ? true : false;
	}

	/**
	 * @return \Nette\Utils\DateTime
	 */
	public function getNextRun()
	{
		return $this->nextRun;
	}

	/**
	 * @param \Nette\Utils\DateTime $nextRun
	 */
	public function setNextRun($nextRun)
	{
		$this->nextRun = $this->DateTime($nextRun);
	}

	/**
	 * @return \Nette\Utils\DateTime
	 */
	public function getLastRun()
	{
		return $this->lastRun;
	}

	/**
	 * @param \Nette\Utils\DateTime $lastRun
	 */
	public function setLastRun($lastRun)
	{
		$this->lastRun = $this->DateTime($lastRun);
	}

	/**
	 * @return boolean
	 */
	public function getLastSuccess()
	{
		return $this->lastSuccess ? true : false;
	}

	/**
	 * @param boolean $lastSuccess
	 */
	public function setLastSuccess($lastSuccess)
	{
		$this->lastSuccess = $lastSuccess ? true : false;
	}


	/**
	 * @return array|null
	 */
	public function getActions()
	{
		return $this->actions;
	}

	/**
	 * Add action
	 *
	 * @param \Mepatek\TaskManager\Entity\TaskAction $action
	 *
	 * @return boolean FALSE - not added
	 */
	public function addAction($action)
	{
		if (is_object($action) and $action instanceof TaskAction) {
			$this->actions[$action->id] = $action;
			return true;
		}
		return false;
	}

	/**
	 * Delete action
	 *
	 * @param int $id
	 */
	public function deleteAction($id)
	{
		if (isset($this->actions[$id])) {
			unset($this->actions[$id]);
		}
	}

	/**
	 * Delete all actions
	 */
	public function deleteAllActions()
	{
		$this->actions = [];
	}


	/**
	 * @return array|null
	 */
	public function getConditions()
	{
		return $this->conditions;
	}

	/**
	 * Add condition
	 *
	 * @param \Mepatek\TaskManager\Entity\TaskCondition $condition
	 *
	 * @return boolean FALSE - not added
	 */
	public function addCondition($condition)
	{
		if (is_object($condition) and $condition instanceof TaskCondition) {
			$this->conditions[$condition->id] = $condition;
			return true;
		}
		return false;
	}

	/**
	 * Delete condition
	 *
	 * @param int $id
	 */
	public function deleteCondition($id)
	{
		if (isset($this->conditions[$id])) {
			unset($this->conditions[$id]);
		}
	}

	/**
	 * Delete all conditions
	 */
	public function deleteAllConditions()
	{
		$this->conditions = [];
	}

	/**
	 * @return TaskHistory[]
	 */
	public function getHistory()
	{
		return $this->history;
	}

	/**
	 * @param TaskHistory[] $history
	 */
	public function setHistory($history)
	{
		$this->history = $history;
	}

	/**
	 * Is history loaded?
	 * @return bool
	 */
	public function isHistoryLoaded()
	{
		return is_array($this->history);
	}


	/**
	 * Run all actions
	 * - set LastRun to now()
	 * - set lastSuccess to true (if all action is ok) or false
	 * - calculate nextRun datetime from all conditions
	 *
	 * @param \Nette\DI\Container                $container
	 * @param string                             $tasksDir
	 * @param \Mepatek\TaskManager\Entity\Output $output
	 *
	 * @return bool TRUE if run all tasks ok
	 */
	public function run($container, $tasksDir, Output $output)
	{
		$success = true;

		// run all actions and set $success
		foreach ($this->actions as $action) {
			// any exception = success false
			try {
				$success = $action->run($container, $tasksDir, $output)
				and $success;
			} catch (Exception $e) {
				$success = false;
			}

		}

		$this->lastSuccess = $success;

		return $success;
	}


	/**
	 * Calculate and set last and next run
	 */
	public function setLastAndNextRun()
	{
		// set lastRun, lastSuccess
		$this->lastRun = new DateTime();

		$this->nextRun = null;
		// find nextTime to run
		foreach ($this->conditions as $condition) {
			$nextRun = $condition->getNextRunTime($this->lastRun);
			// if nextRun less than lastRun set lastRun + 1min
			if ($nextRun < $this->lastRun) {
				$nextRun = $this->lastRun->add(new \DateInterval("PT1M"));
			}
			if ($this->nextRun) {
				$this->nextRun = ($this->nextRun > $nextRun) ? $nextRun : $this->nextRun;
			} else {
				$this->nextRun = $nextRun;
			}
		}
	}

}

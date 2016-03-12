<?php


namespace Mepatek\TaskManager\Entity;

use Nette\Utils\DateTime;

use Mepatek\Entity\AbstractEntity;

/**
 * Class TaskHistory
 * @package Mepatek\TaskManager\Entity
 */
class TaskHistory extends AbstractEntity
{

	/** @var integer */
	protected $id = null;
	/** @var integer */
	protected $taskId;
	/** @var \Nette\Utils\DateTime */
	protected $started;
	/** @var \Nette\Utils\DateTime */
	protected $finished;
	/** @var integer */
	protected $resultCode;
	/** @var string 150 */
	protected $user;
	/** @var Output */
	protected $output;

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
	 * @return int
	 */
	public function getTaskId()
	{
		return $this->taskId;
	}

	/**
	 * @param int $taskId
	 */
	public function setTaskId($taskId)
	{
		$this->taskId = $taskId;
	}

	/**
	 * @return DateTime
	 */
	public function getStarted()
	{
		return $this->started;
	}

	/**
	 * @param DateTime $started
	 */
	public function setStarted($started)
	{
		$this->started = $this->DateTime($started);
	}

	/**
	 * @return DateTime
	 */
	public function getFinished()
	{
		return $this->finished;
	}

	/**
	 * @param DateTime $finished
	 */
	public function setFinished($finished)
	{
		$this->finished = $this->DateTime($finished);
	}

	/**
	 * @return int
	 */
	public function getResultCode()
	{
		return $this->resultCode;
	}

	/**
	 * @param int $resultCode
	 */
	public function setResultCode($resultCode)
	{
		$this->resultCode = (int)$resultCode;
	}

	/**
	 * @return string
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param string $user
	 */
	public function setUser($user)
	{
		$this->user = $this->StringTruncate($user, 150);
	}

	/**
	 * @return Output
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * @param Output|string $output
	 */
	public function setOutput($output)
	{
		if ($output instanceof Output) {
			$this->output = $output;
		} else {
			$this->output = new Output();
			$this->output->output = $output;
		}
	}

}

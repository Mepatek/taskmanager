<?php

namespace Mepatek\TaskManager\Entity;

use Mepatek\Entity\AbstractEntity;


/**
 * Class TaskCondition
 * @package Mepatek\TaskManager\Entity
 */
abstract class TaskCondition extends AbstractEntity implements ITaskCondition
{

	/** @var integer */
	protected $id;
	/** @var \Nette\Utils\DateTime */
	protected $created;
	/** @var \Nette\Utils\DateTime */
	protected $expired;
	/** @var boolean */
	protected $active;


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
	 * @return \Nette\Utils\DateTime
	 */
	public function getExpired()
	{
		return $this->expired;
	}

	/**
	 * @param \Nette\Utils\DateTime $expired
	 */
	public function setExpired($expired)
	{
		$this->expired = $this->DateTime($expired);
	}

	/**
	 * @return boolean
	 */
	public function getActive()
	{
		return $this->active ? true : false;
	}

	/**
	 * @param boolean $active
	 */
	public function setActive($active)
	{
		$this->active = $active ? true : false;
	}


}

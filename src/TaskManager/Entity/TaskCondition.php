<?php


namespace Mepatek\TaskManager\Entity;


/**
 * Class TaskCondition
 * @package Mepatek\TaskManager\Entity
 */
abstract class TaskCondition extends AbstractEntity
{

	/** @var integer */
	protected $id;
	/** @var string 30 */
	protected $type;
	/** @var mixed save as JSON string */
	protected $data;
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
		if ( ! $this->id ) {
			$this->id = (int)$id;
		}
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $this->StringTruncate($type, 30);
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$this->data = $data;
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
		return $this->active ? TRUE : FALSE;
	}

	/**
	 * @param boolean $active
	 */
	public function setActive($active)
	{
		$this->active = $active ? TRUE : FALSE;
	}


}

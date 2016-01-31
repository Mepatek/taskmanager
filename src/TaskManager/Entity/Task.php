<?php


namespace Mepatek\TaskManager\Entity;

/**
 * Class Task
 * @package Mepatek\TaskManager\Entity
 */
class Task extends AbstractEntity
{

	/** @var integer */
	protected $id = NULL;
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
	protected $deleteAfterRun;
	/** @var integer */
	protected $state;
	/** @var \Nette\Utils\DateTime */
	protected $nextRun;
	/** @var \Nette\Utils\DateTime */
	protected $lastRun;

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
		return $this->deleteAfterRun ? TRUE : FALSE;
	}

	/**
	 * @param boolean $deleteAfterRun
	 */
	public function setDeleteAfterRun($deleteAfterRun)
	{
		$this->deleteAfterRun = $deleteAfterRun ? TRUE : FALSE;
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

}

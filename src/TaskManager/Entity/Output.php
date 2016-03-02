<?php

namespace Mepatek\TaskManager\Entity;


/**
 * Class Output
 * @package Mepatek\TaskManager\Entity
 */
class Output extends AbstractEntity
{
	/** @var string */
	protected $output = "";
	/** @var string */
	protected $error = null;

	/**
	 * @return string
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * @param string $output
	 */
	public function setOutput($output)
	{
		$this->output = $output;
	}

	/**
	 * Add to output text
	 * @param string $text
	 */
	public function write($text)
	{
		$this->output .= (string)$text . "\n";
	}

	/**
	 * @return string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param string $error
	 */
	public function setError($error)
	{
		$this->error = $error;
	}

	/**
	 * Add to error text
	 * @param string $text
	 */
	public function error($text)
	{
		$this->write ("ERROR:" . (string)$text);
		$this->error .= (string)$text . "\n";
	}

	/**
	 * Return output
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->output;
	}
}
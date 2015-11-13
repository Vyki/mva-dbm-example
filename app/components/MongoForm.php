<?php

namespace App\Components;

use Mva\Dbm\Helpers,
	Nette\Application\UI\Form,
	Nette\ComponentModel\IComponent;

class MongoForm extends Form
{

	/** @var array */
	private $dict = [];

	/** @var string */
	private $delimiter = '_x_';

	public function getComponent($name, $need = TRUE)
	{

		if (isset($this->dict[$name])) {
			$name = $this->dict[$name];
		}

		return parent::getComponent($name, $need);
	}

	public function addComponent(IComponent $component, $name, $insertBefore = NULL)
	{
		if (strpos($name, '.') !== FALSE) {
			$alias = str_replace('.', $this->delimiter, $name);
			$this->dict[$name] = $alias;
			$name = $alias;
		}

		return parent::addComponent($component, $name, $insertBefore);
	}

	public function getValues($asArray = FALSE)
	{
		$values = parent::getValues(TRUE);

		if ($asArray) {
			return Helpers::expandArray($values, $this->delimiter);
		}

		$return = [];

		foreach ($values as $key => $value) {
			$return[str_replace($this->delimiter, '.', $key)] = $value;
		}

		return $return;
	}

	public function setValues($values, $erase = FALSE)
	{
		$values = Helpers::contractArray($values, $this->delimiter, TRUE);
		return parent::setValues($values, $erase);
	}

}

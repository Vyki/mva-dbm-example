<?php

namespace App\Components;

use Nette,
	Nette\Application\UI\Control,
	Mva\Dbm\Collection\Selection;

class RestaurantList extends Control
{

	/** @var int */
	private $count;

	/** @var int */
	private $limit = 10;

	/** @var Selection */
	private $selection;

	public function setLimit($limit)
	{
		$this->limit = (int) $limit;
	}

	public function setSelection(Selection $selection)
	{
		$this->selection = $selection;
		$this->selection->select('cuisine', 'name', 'borough', 'restaurant_id');
		$this->count = $selection->count('*');
	}

	public function render()
	{
		if ($this->selection === NULL) {
			throw Nette\InvalidStateException('Selection was not initialized!');
		}

		$this['vp']->setItemCount($this->count);
		$this['vp']->setItemsPerPage($this->limit);

		$this->selection->limit($this->limit, $this['vp']->paginator->offset);

		$this->template->count = $this->count;
		$this->template->restaurants = $this->selection;

		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->render();
	}

	protected function createComponentVp()
	{
		return new VisualPaginator();
	}

}

interface IRestaurantListFactory
{

	/** @return RestaurantList */
	function create();
}

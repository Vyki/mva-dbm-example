<?php

namespace App\Components;

use Nette\Application\UI\Form,
	Nette\Application\UI\Control,
	Mva\Dbm\Collection\Document,
	Mva\Dbm\Collection\Selection;

class RestaurantEditor extends Control
{

	/** @var callable[] */
	public $onSave = [];

	/** @var callable[] */
	public $onError = [];

	/** @var array */
	private $data = [];

	/** @var array */
	private $cuisine = [];

	/** @var array */
	private $borough = [];

	public function setData(Document $data)
	{
		$this['restForm']->setDefaults($data->toArray());
	}

	public function setBoroughList(array $data)
	{
		$this->borough = $data;
	}

	public function setCuisineSuggest(Selection $data)
	{
		$this->cuisine = $data;
	}

	protected function createComponentRestForm()
	{
		$form = new MongoForm();

		$form->addText('name', 'Name')->setRequired();

		$form->addText('cuisine', 'Cuisine')->setRequired();

		$form->addSelect('borough', 'Borough', $this->borough)->setPrompt('- select borough -')->setRequired();

		$form->addText('address.building', 'Building')->setRequired();

		$form->addText('address.street', 'Street')->setRequired();

		$form->addText('address.zipcode', 'Zipcode')->setRequired();

		$form->addText('address.coord.0', 'Latitude')->addRule(MongoForm::FLOAT)->setRequired();

		$form->addText('address.coord.1', 'longtitude')->addRule(MongoForm::FLOAT)->setRequired();

		$form->addSubmit('save', 'Save');

		$form->onSuccess[] = function($form, $data) {
			$this->onSave($data);
		};

		return $form;
	}

	public function render()
	{
		$this['restForm']->setDefaults($this->data);

		$this->template->cuisine = $this->cuisine->fetchPairs(NULL, 'cuisine');
		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->render();
	}

}

interface IRestaurantEditorFactory
{

	/** @return RestaurantEditor */
	function create();
}

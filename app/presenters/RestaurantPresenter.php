<?php

namespace App\Presenters;

use Nette,
	App\Model,
	App\Components,
	Nette\Application\UI\Form;

class RestaurantPresenter extends BasePresenter
{

	/** @var Model\Restaurants @inject */
	public $restaurants;

	/** @var Components\IRestaurantListFactory @inject */
	public $restaurantListFactory;

	/** @var Components\IRestaurantEditorFactory @inject */
	public $restaurantEditorFactory;

	public function renderDefault($fkey = NULL, $fval = NULL)
	{
		if (!in_array($fkey, [NULL, 'cuisine', 'borough', 'name'])) {
			throw new Nette\Application\BadRequestException('Wrong filtering parameters!');
		}

		$selection = $this->restaurants->findAll();

		if ($fkey && $fval) {
			$fkey === 'name' ? $selection->where("$fkey LIKE", $fval) : $selection->where("$fkey = %s", $fval);
		}

		$this['restaurantList']->setSelection($selection);

		$this->template->fkey = $fkey;
		$this->template->fval = $fval;
	}

	public function renderSelect($fkey)
	{
		if (!in_array($fkey, ['cuisine', 'borough'])) {
			throw new Nette\Application\BadRequestException('Wrong filtering parameters!');
		}

		$this->template->fkey = $fkey;
		$this->template->cuisines = $this->restaurants->findCountBy($fkey);
	}

	public function renderDetail($id)
	{
		$selection = $this->restaurants->findAll()->wherePrimary($id);
		$this->template->restaurant = $selection->fetch();
	}

	public function actionAdd()
	{
		$this['restaurantEditor']->onSave[] = function ($data) {
			$data['grades'] = [];
			$data['restaurant_id%s'] = $this->restaurants->getLastId() + 1;

			$doc = $this->restaurants->insert($data);

			$this->flashMessage("Restaurant {$data['name']} successfully added!", 'success');
			$this->redirect('detail', $doc->_id);
		};

		$this->setView('editor');
	}

	public function actionEdit($id)
	{
		$this->setView('editor');

		$this['restaurantEditor']->onSave[] = function ($data) use ($id) {
			$doc = $this->restaurants->update($id, $data);

			$this->flashMessage("Restaurant {$data['name']} was successfully saved!", 'success');
			$this->redirect('detail', $id);
		};

		$this['restaurantEditor']->setData($this->restaurants->getBy($id));

		$this->setView('editor');
	}

	public function handleDelete($id)
	{
		$this->restaurants->delete($id);
		$this->flashMessage("Restaurant #$id was successfully deleted!", 'success');
		$this->redirect('this');
	}

	############## component factories ############## 

	protected function createComponentRestaurantList()
	{
		return $this->restaurantListFactory->create();
	}

	protected function createComponentRestaurantEditor()
	{
		$editor = $this->restaurantEditorFactory->create();
		$editor->setBoroughList($this->restaurants->getBoroughList());
		$editor->setCuisineSuggest($this->restaurants->findCountBy('cuisine'));
		return $editor;
	}

	protected function createComponentSearchForm()
	{
		$form = new Nette\Application\UI\Form();

		$form->addText('name', 'Restaurant')
				->setDefaultValue($this->getParameter('fval'))
				->setRequired();

		$form->addSubmit('search', 'Search');

		$form->onSuccess[] = function($form, $values) {
			$this->redirect('default', 'name', $values['name']);
		};

		return $form;
	}

	protected function createComponentGradeForm()
	{
		$form = new Form();

		$form->addText('grade', 'Grade')
				->addRule(Form::PATTERN, 'Grade must be a letter A-Z', '[a-zA-Z]')
				->setRequired();

		$form->addText('score', 'Score')
				->addRule(Form::INTEGER, 'Score must be a number')
				->addRule(Form::RANGE, 'Score must be in range from %d to %d', [0, 25])
				->setRequired();

		$form->addSubmit('save', 'Save');

		$form->onSuccess[] = function($form, $values) {
			$this->restaurants->insertGrade($this->getParameter('id'), $values['score'], $values['grade']);
			$this->redirect('this');
		};

		return $form;
	}

}

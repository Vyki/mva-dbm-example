<?php
namespace App\Components;

use Nette;

class VisualPaginator extends Nette\Application\UI\Control
{
	/** @var Paginator */
	private $paginator;

	/** @persistent */
	public $page = 1;


	/**
	 * @return Nette\Paginator
	 */
	public function getPaginator()
	{
		if (!$this->paginator) {
			$this->paginator = new Nette\Utils\Paginator();
		}
		return $this->paginator;
	}

        /**
         * @param $limit
         * @return $this
         */
        public function setItemsPerPage($limit)
        {
            $this->getPaginator()->setItemsPerPage($limit);
            return $this;
        }
        
        /**
         * @param $count
         * @return $this
         */
        public function setItemCount($count)
        {
            $this->getPaginator()->setItemCount((int) $count);
            return $this;
        }

	/**
	 * Renders paginator.
	 * @return void
	 */
	public function render()
	{
		$paginator = $this->getPaginator();
                
		$page = $paginator->page;
                
		if ($paginator->pageCount < 2) {
			$steps = array($page);

		} else {
			$arr = range(max($paginator->firstPage, $page - 3), min($paginator->lastPage, $page + 3));
			
                        $count = 4;
			
                        $quotient = ($paginator->pageCount - 1) / $count;
			
                        for ($i = 0; $i <= $count; $i++) {
				$arr[] = round($quotient * $i) + $paginator->firstPage;
			}
			sort($arr);
                        
			$steps = array_values(array_unique($arr));
		}

		$this->template->steps = $steps;
		$this->template->paginator = $paginator;
		$this->template->setFile(__DIR__ . '/template.latte');
		$this->template->render();
	}



	/**
	 * Loads state informations.
	 * @param  array
	 * @return void
	 */
	public function loadState(array $params)
	{
		parent::loadState($params);
		$this->getPaginator()->page = $this->page;
	}
}
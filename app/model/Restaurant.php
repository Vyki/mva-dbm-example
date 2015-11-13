<?php

namespace App\Model;

use Mva\Dbm\Collection\Selection;

class Restaurants extends BaseModel
{

	/** @var string collection name */
	private $name = 'restaurants';

	public function getName()
	{
		return $this->name;
	}

	###################### get methods #########################

	/** @return int */
	public function getLastId()
	{
		return (int) $this->findAll()->max('restaurant_id');
	}

	/** @return array */
	public function getBoroughList()
	{
		return [
			'Manhattan' => 'Manhattan',
			'Brooklyn' => 'Brooklyn',
			'Queens' => 'Queens',
			'Bronx' => 'Bronx',
			'Staten Island' => 'Staten Island',
			'Missing' => 'Missing'
		];
	}

	###################### find methods ########################

	/** @return Selection */
	public function findCountBy($group)
	{
		return $this->findAll()->select('SUM(*) AS count')->group($group)->order('count DESC');
	}

	################## manipulation methods ####################

	public function insertGrade($id, $score, $grade)
	{
		$update = $this->update($id, [
			'$push' => [
				'grades' => [
					'date%dt' => 'now',
					'score%i' => $score,
					'grade%s' => $grade
				]
			]
		]);

		return $update;
	}

}

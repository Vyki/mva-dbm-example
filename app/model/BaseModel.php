<?php

namespace App\Model;

use Mva,
	Mva\Dbm\Collection\Document,
	Mva\Dbm\Collection\Selection;

abstract class BaseModel
{

	/** @var Mva\Dbm\Connection */
	private $connection;

	/** @var string primary key condition */
	protected $primary = '_id = %oid';

	/** @var string */
	abstract public function getName();

	public function __construct(Mva\Dbm\Connection $connection)
	{
		$this->connection = $connection;
	}

	######################## select methods #########################

	/** @return Document|NULL */
	public function getBy($condition)
	{
		return $this->findBy($condition)->limit(1)->fetch();
	}

	/** @return Selection */
	public function findBy($condition)
	{
		return $this->findAll()->where($this->modifyCondition($condition));
	}

	/** @return Selection */
	public function findAll()
	{
		return $this->connection->getSelection($this->getName());
	}

	######################## manipulation methods #########################

	/** @return int */
	public function update($condition, array $data)
	{
		return $this->findAll()->where($this->modifyCondition($condition))->update($data);
	}

	/** @return Document|int */
	public function upsert($condition, array $data)
	{
		return $this->findAll()->where($this->modifyCondition($condition))->update($data, TRUE);
	}

	/** @return Document */
	public function insert(array $data)
	{
		return $this->findAll()->insert($data);
	}

	/** @return int */
	public function delete($condition)
	{
		return $this->findAll()->where($this->modifyCondition($condition))->delete();
	}

	######################## helper methods ########################

	protected function modifyCondition($condition)
	{
		return is_array($condition) ? $condition : [$this->primary => $condition];
	}

}
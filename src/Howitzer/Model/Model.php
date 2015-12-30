<?php
namespace Howitzer\Model;

abstract class Model
{
	protected $db;
	
	public function __construct (\PDO $aDB)
	{
		$this->db = $aDB;
	}
}
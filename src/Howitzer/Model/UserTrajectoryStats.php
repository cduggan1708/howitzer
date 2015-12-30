<?php
namespace Howitzer\Model;

use Howitzer\Model\Dao\HowitzerDao;
class UserTrajectoryStats extends Model
{
	private $log;
	
	public function __construct(\PDO $aDB, $aLog)
	{
		parent::__construct($aDB);
		$this->log = $aLog;
	}
	
	public function getTotalShotsByUsername($aUsername)
	{
		$dao = new HowitzerDao($this->db);
		return $dao->getTotalShotsByUsername($aUsername);
	}
	
	public function getTotalShotsAllUsers()
	{
		$dao = new HowitzerDao($this->db);
		return $dao->getTotalShotsAllUsers();
	}
	
	public function getTotalUsers()
	{
		$dao = new HowitzerDao($this->db);
		return $dao->getTotalUsers();
	}
	
	public function getAverageNumShotsToHitTarget($aTotalShots)
	{
		$dao = new HowitzerDao($this->db);
		$totalTargetsHit = $dao->getTotalShotsHitTarget();
		if ($totalTargetsHit != null && $totalTargetsHit != 0) {
			return number_format((float) ($aTotalShots / $totalTargetsHit), 2, '.', '');
		}
		
		return 'no targets hit yet';
	}
	
	public function getOrderedAverageDistanceFromTarget()
	{
		$dao = new HowitzerDao($this->db);
		return $dao->getOrderedDistanceFromTargetAllUsers();
	}
}
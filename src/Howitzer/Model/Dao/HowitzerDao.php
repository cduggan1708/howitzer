<?php
namespace Howitzer\Model\Dao;

use Howitzer\Model\Howitzer;

class HowitzerDao
{
	private $db;
	
	public function __construct (\PDO $aDB)
	{
		$this->db = $aDB;
	}
	
	public function onSave(Howitzer $aHowitzer)
	{
		if (isset($aHowitzer) && $aHowitzer != null) {
			$userId = $this->getOrSaveUser($aHowitzer);
			
			if ($userId != 0) {
				$this->saveUserTrajectory($aHowitzer, $userId);
			} else {
				throw new \PDOException('Error trying to save user with username: ' . $aHowitzer->username);
			}
		} else {
				throw new \PDOException('Error trying to save user and trajectory due to empty data');
			}
	}
	
	public function getTotalShotsByUsername($aUsername)
	{
		$totalShots = 0;
		
		$userId = $this->getUserIdByUsername($aUsername);
		
		if ($userId != null && $userId != 0) {
			$ps = $this->db->prepare(self::GET_TOTAL_SHOTS_BY_USER_SQL);
			$ps->setFetchMode(\PDO::FETCH_OBJ);
			$ps->bindParam(':userId', $userId);
			$ps->execute();
			$row = $ps->fetch();
			if (isset($row) && $row != null) {
				$totalShots = $row->total;
			}
		}
		
		return $totalShots;
	}
	
	public function getTotalShotsAllUsers()
	{
		$totalShots = 0;
	
		$ps = $this->db->prepare(self::GET_TOTAL_SHOTS_SQL);
		$ps->setFetchMode(\PDO::FETCH_OBJ);
		$ps->execute();
		$row = $ps->fetch();
		if (isset($row) && $row != null) {
			$totalShots = $row->total;
		}
	
		return $totalShots;
	}
	
	public function getTotalUsers()
	{
		$totalUsers = 0;
	
		$ps = $this->db->prepare(self::GET_TOTAL_USERS_SQL);
		$ps->setFetchMode(\PDO::FETCH_OBJ);
		$ps->execute();
		$row = $ps->fetch();
		if (isset($row) && $row != null) {
			$totalUsers = $row->total;
		}
	
		return $totalUsers;
	}
	
	public function getTotalShotsHitTarget()
	{
		$totalShotHitTarget = 0;
		
		$ps = $this->db->prepare(self::GET_TOTAL_SHOTS_HIT_TARGET_SQL);
		$ps->setFetchMode(\PDO::FETCH_OBJ);
		$ps->execute();
		$row = $ps->fetch();
		if (isset($row) && $row != null) {
			$totalShotHitTarget = $row->total;
		}
		
		return $totalShotHitTarget;
	}
	
	public function getTotalShotsHitTargetByUsername($aUsername)
	{
		$totalShotHitTarget = 0;
		
		$userId = $this->getUserIdByUsername($aUsername);
		
		if ($userId != null && $userId != 0) {
	
			$ps = $this->db->prepare(self::GET_TOTAL_SHOTS_HIT_TARGET_BY_USER_SQL);
			$ps->setFetchMode(\PDO::FETCH_OBJ);
			$ps->bindParam(':userId', $userId);
			$ps->execute();
			$row = $ps->fetch();
			if (isset($row) && $row != null) {
				$totalShotHitTarget = $row->total;
			}
		}
	
		return $totalShotHitTarget;
	}
	
	public function getOrderedDistanceFromTargetAllUsers()
	{
		$array = null;
		
		$ps = $this->db->prepare(self::GET_ORDERED_DISTANCE_FROM_TARGET_SQL);
		$ps->setFetchMode(\PDO::FETCH_OBJ);
		$ps->execute();
		$userId = 0;
		$numShots = 0;
		$avgDistanceFromTarget = 0;
		$i = 0;
		while ($row = $ps->fetch()) {
			if (isset($row) && $row != null) {
				$username = $this->getUsernameById($row->user_id);
				$numShots = $row->total;
				$avgDistanceFromTarget = number_format((float) $row->avgDistance, 2, '.', '');
			}
			
			if ($numShots != 0) {
				$array[$i] = array("username"=>$username, "numShots"=>$numShots, "avgDistanceFromTarget"=>$avgDistanceFromTarget);
			}
			
			$i++;
		}
		
		return $array;
	}
	
	private function getUserIdByUsername($aUsername)
	{
		$userId = 0;
		
		if ($aUsername != null && $aUsername != "") {
		
			// check if user already exists
			$ps = $this->db->prepare(self::GET_USER_SQL);
			$ps->setFetchMode(\PDO::FETCH_OBJ);
			$ps->bindParam(':name', $aUsername);
			$ps->execute();
			$row = $ps->fetch();
			if (isset($row) && $row != null) {
				$userId = $row->id;
			}
		}
		
		return $userId;
	}
	
	private function getUsernameById($aUserId)
	{
		$username = null;
	
		if ($aUserId != null && $aUserId != 0) {
	
			// check if user already exists
			$ps = $this->db->prepare(self::GET_USERNAME_SQL);
			$ps->setFetchMode(\PDO::FETCH_OBJ);
			$ps->bindParam(':id', $aUserId);
			$ps->execute();
			$row = $ps->fetch();
			if (isset($row) && $row != null) {
				$username = $row->name;
			}
		}
	
		return $username;
	}
	
	private function getOrSaveUser(Howitzer $aHowitzer)
	{
		$userId = 0;
		
		// prepared statements
		if ($aHowitzer->username != null && $aHowitzer->username != "") {
		
			// check if user already exists
			$userId = $this->getUserIdByUsername($aHowitzer->username);
				
			// save user if needed
			if ($userId == 0) {
				$ps2 = $this->db->prepare(self::INSERT_USER_SQL);
				$ps2->bindParam(':name', $aHowitzer->username);
				$date = date("Y-m-d H:i:s");
				$ps2->bindParam(':created', $date);
				if ($ps2->execute()) {
					$userId = $this->db->lastInsertId();
				}
			}
		}
		
		return $userId;
	}
	
	private function saveUserTrajectory(Howitzer $aHowitzer, $aUserId)
	{
		// prepared statements
		if ($aHowitzer->shellWeight != null && $aHowitzer->targetDistance != null && $aHowitzer->targetLength != null && $aHowitzer->targetWidth != null
			&& $aHowitzer->barrelAngle != null && $aHowitzer->muzzleVelocity != null && $aUserId != null && $aUserId != 0) {
			
			$ps = $this->db->prepare(self::INSERT_USER_TRAJECTORY_SQL);
			$ps->bindParam(':userId', $aUserId);
			$date = date("Y-m-d H:i:s");
			$ps->bindParam(':date', $date);
			$ps->bindParam(':shellWeight', $aHowitzer->shellWeight);
			$ps->bindParam(':targetDistance', $aHowitzer->targetDistance);
			$ps->bindParam(':targetLength', $aHowitzer->targetLength);
			$ps->bindParam(':targetWidth', $aHowitzer->targetWidth);
			$ps->bindParam(':barrelAngle', $aHowitzer->barrelAngle);
			$ps->bindParam(':muzzleVelocity', $aHowitzer->muzzleVelocity);
			$ps->bindParam(':shellDistance', $aHowitzer->shellDistanceTraveled);
			$ps->bindParam(':targetHit', $aHowitzer->targetHit);
			$ps->bindParam(':distanceFromTarget', $aHowitzer->targetMissedDistance);
			$ps->bindParam(':missedDirection', $aHowitzer->targetMissedDirection);
			$ps->execute();
			
		} else {
			throw new \PDOException('Error trying to save user trajectory due to empty data for username ' . $aHowitzer->username);
		}
	}
	
	const GET_USER_SQL = "SELECT `id` FROM user WHERE `name`=:name";
	const GET_USERNAME_SQL = "SELECT `name` FROM user WHERE `id`=:id";
	const INSERT_USER_SQL = "INSERT INTO user (`name`,`create_date`) VALUES (:name, :created)";
	const INSERT_USER_TRAJECTORY_SQL = "INSERT INTO user_trajectory VALUES (:userId, :date, :shellWeight, :targetDistance, :targetLength, :targetWidth, :barrelAngle, :muzzleVelocity,
			:shellDistance, :targetHit, :distanceFromTarget, :missedDirection)";
	
	const GET_TOTAL_SHOTS_SQL = "SELECT COUNT(*) as `total` FROM user_trajectory";
	const GET_TOTAL_SHOTS_BY_USER_SQL = "SELECT COUNT(*) as `total` FROM user_trajectory WHERE `user_id`=:userId";
	const GET_TOTAL_USERS_SQL = "SELECT COUNT(*) as `total` FROM user";
	const GET_TOTAL_SHOTS_HIT_TARGET_SQL = "SELECT COUNT(*) as `total` FROM user_trajectory WHERE `targetHit`='true'";
	const GET_TOTAL_SHOTS_HIT_TARGET_BY_USER_SQL = "SELECT COUNT(*) as `total` FROM user_trajectory WHERE `targetHit`='true' AND `user_id`=:userId";
	const GET_ORDERED_DISTANCE_FROM_TARGET_SQL = "SELECT `user_id`, COUNT(*) as `total`, AVG(`distanceFromTarget`) as `avgDistance` FROM user_trajectory group by `user_id` order by `avgDistance` asc";
}
<?php
namespace Howitzer\Model;

use Howitzer\Model\Dao\HowitzerDao;
class Howitzer extends Model
{
	/**
	 * shell weight in kg
	 */
	public $shellWeight;
	
	/**
	 * distance to target in meters
	 */
	public $targetDistance;
	
	/**
	 * target length in meters
	 */
	public $targetLength;
	
	/**
	 * target width in meters
	 */
	public $targetWidth;
	
	/**
	 * angle of barrel from ground in degrees
	 */
	public $barrelAngle;
	
	/**
	 * speed of muzzle in meters/second
	 */
	public $muzzleVelocity;
	
	/**
	 * whether target was hit or not
	 */
	public $targetHit;
	
	/**
	 * if target missed, by how far in meters?
	 */
	public $targetMissedDistance;
	
	/**
	 * if target missed, was it too far or too short?
	 */
	public $targetMissedDirection;
	
	/**
	 * how far did shell travel in meters
	 */
	public $shellDistanceTraveled;
	
	/**
	 * user hitting 'Fire'
	 */
	public $username;
	
	/**
	 * for logging
	 */
	private $log;
	
	
	
	public function __construct(\PDO $aDB, $aLog)
	{
		parent::__construct($aDB);
		$this->log = $aLog;
	}
	
	/**
	 * PHP doesn't allow multiple constructors so treat this as a method
	 * @param $aShellWeight
	 * @param $aTargetDistance
	 * @param $aTargetLength
	 * @param $aTargetWidth
	 * @param $aBarrelAngle
	 * @param $aMuzzleVelocity
	 * @param $aUsername
	 */
	public function build($aShellWeight, $aTargetDistance, $aTargetLength, $aTargetWidth, $aBarrelAngle, $aMuzzleVelocity, $aUsername)
	{
	    $this->shellWeight = $aShellWeight;
	    $this->targetDistance = $aTargetDistance;
	    $this->targetLength = $aTargetLength;
	    $this->targetWidth = $aTargetWidth;
	    $this->barrelAngle = $aBarrelAngle;
	    $this->muzzleVelocity = $aMuzzleVelocity;
	    $this->username = $aUsername;
	    
	    $this->calculateTrajectory();
	}
	
	/**
	 * calculates trajectory, given the variables in $this->build
	 * sets variables $this->targetHit and $this->shellDistanceTraveled
	 */
	public function calculateTrajectory()
	{
		$initialVelocitySquared = (pow($this->muzzleVelocity, 2));
		$barrelRadians = deg2rad($this->barrelAngle);
		
		// simplest equation, not taking air drag or wind into consideration for now
		$this->shellDistanceTraveled = number_format((float) (($initialVelocitySquared * sin(2 * $barrelRadians)) / 9.8), 2, '.', '');
		
		// just taking length of target into consideration for now (ignoring wind)
		$minDistanceToTarget = $this->targetDistance;
		$maxDistanceToTarget = $this->targetDistance + $this->targetLength;
		if ($this->shellDistanceTraveled >= $minDistanceToTarget && ($this->shellDistanceTraveled <= $maxDistanceToTarget)) {
			$this->targetHit = 'true';
			$this->targetMissedDistance = 0;
		} else {
			$this->targetHit = 'false';
			
			if ($this->shellDistanceTraveled < $minDistanceToTarget) {
				$this->targetMissedDistance = $minDistanceToTarget - $this->shellDistanceTraveled;
				$this->targetMissedDirection = 'short of';
			} else {
				$this->targetMissedDistance = $this->shellDistanceTraveled - $maxDistanceToTarget;
				$this->targetMissedDirection = 'past';
			}
		}
	}
	
	public function getInitialSettings()
	{
		$this->shellWeight = rand(40, 100);
		$this->targetDistance = rand(400, 14000);
		$this->targetLength = rand(1, 100);
		$this->targetWidth = $this->targetLength;
		
		return $this;
	}
	
	public function storeData()
	{
		$dao = new HowitzerDao($this->db);
		$dao->onSave($this);
	}
}
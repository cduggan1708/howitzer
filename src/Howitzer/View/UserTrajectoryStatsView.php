<?php
namespace Howitzer\View;

use Howitzer\Model\UserTrajectoryStats;

class UserTrajectoryStatsView
{
	public function __construct()
	{
	
	}
	
	public function onDisplay(UserTrajectoryStats $aStats, $aUsername=null)
	{
		echo <<<STATS
<!DOCTYPE html>
<html lang=en>
<form>
<article class="bottom-right">
STATS;
		if ($aUsername != null) {
			$totalShotsForUser = $aStats->getTotalShotsByUsername($aUsername);
			echo <<<STATS
			<p>Total Shots for Username $aUsername: $totalShotsForUser</p>
STATS;
		}
	
		$totalShotsAllUsers = $aStats->getTotalShotsAllUsers();
		$totalUsers = $aStats->getTotalUsers();
		$avgNumShotsToHitTarget = $aStats->getAverageNumShotsToHitTarget($totalShotsAllUsers);
		echo <<<STATS
		<p>Total Shots for All Users: $totalShotsAllUsers</p>
		<p>Total Users: $totalUsers</p>
		<p>Avg Number Shots to Hit Target: $avgNumShotsToHitTarget</p>
		<ul>
			<h3>Top Users</h3>
STATS;
		$currentUserRank = 0;
		$i = 0;
		foreach ($aStats->getOrderedAverageDistanceFromTarget() as $userArray) {
			if ($i > 4) {
				if ($currentUserRank != 0 || $aUsername == null) {
					break;
				} else {
					$i++;
					$username = $userArray["username"];
					if ($aUsername == $username) {
						$currentUserRank = $i;
					}
					continue;
				}
			}
			$i++;
			$username = $userArray["username"];
			if ($aUsername != null && $aUsername == $username) {
				$currentUserRank = $i;
			}
			$accuracyTotal = $userArray['numShots'];
			$avgDistance = $userArray['avgDistanceFromTarget'];
			echo <<<STATS
			<li>#$i $username</li>
			<ul>
			    <li>Total Number of Shots: $accuracyTotal</li>
			    <li>Average Distance from Target: $avgDistance</li>
			</ul>
STATS;
		}
		echo '</ul>';
		if ($currentUserRank != 0) {
			echo <<<STATS
			<p>Current Ranking: #$currentUserRank</p>
STATS;
		}
		echo <<<STATS
</article>
</form>
</html>

STATS;
		
	}
}
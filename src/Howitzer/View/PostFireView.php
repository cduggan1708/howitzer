<?php
namespace Howitzer\View;

use Howitzer\Model\Howitzer;

class PostFireView
{
	public function __construct()
	{
		
	}
	
	public function onDisplay(Howitzer $aHowitzer)
	{
		echo <<<HOWITZER
<!DOCTYPE html>
<html lang=en>
<meta charset=utf-8>
<link rel="stylesheet" href="css/howitzer.css" type="text/css" />
<title>Howitzer Shooting Practice</title>

<form method="GET" action="index.php">
<header>
<h1>Howitzer Shooting Practice</h1>
</header>
				
<fieldset>
	<legend>Howitzer Settings</legend>
	<p>Shell Weight: $aHowitzer->shellWeight kg</p>
	<p>Distance to Target: $aHowitzer->targetDistance m</p>
	<p>Size of Target: $aHowitzer->targetLength m x $aHowitzer->targetWidth m</p>
	<p>Angle of Barrel from Ground: $aHowitzer->barrelAngle &deg;</p>
	<p>Muzzle Velocity: $aHowitzer->muzzleVelocity m/s</p>
</fieldset>
			
<fieldset>
	<legend>Trajectory Results</legend>
	<p>Username: $aHowitzer->username</p>
	<p>Distance Traveled: $aHowitzer->shellDistanceTraveled m</p>
	<p>Was Target Hit?: $aHowitzer->targetHit</p>
HOWITZER;
	    if ($aHowitzer->targetHit == 'false') {
	        echo <<< TARGETMISSED
	<p>Shell landed $aHowitzer->targetMissedDistance m $aHowitzer->targetMissedDirection target</p>
TARGETMISSED;
	    }
        echo <<< HOWITZER
</fieldset>
			
<fieldset>
	<legend>Start Over</legend>
	<input type="submit" class="button" value="Try Again" />
</fieldset>
</form>
</html>
		
HOWITZER;
	}
}
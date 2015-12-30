<?php
namespace Howitzer\View;

use Howitzer\Model\Howitzer;

class PreFireView
{
	public function __construct()
	{
		
	}
	
	public function onDisplay(Howitzer $aHowitzer)
	{
		echo <<<HTML
<!DOCTYPE html>
<html lang=en>
<meta charset=utf-8>
<link rel="stylesheet" href="css/howitzer.css" type="text/css" />
<script type="text/javascript" language="javascript">
<!--
/*variables*/

var barrelAngle;
var muzzleVelocity;
var username;

/*function to do all validation*/
function validate(){
    /*variable to keep track of number of errors*/
	var counter = 0;
	/*variable to hold error messages*/
	var strMsg = "<strong>The following errors must be corrected before the trajectory can be calculated:</strong><br /><br />";
	var p_error_handle = document.getElementById("p_error");
	

	if(isEmpty(getBarrelAngle())){
		strMsg += "<li>Enter barrel angle.</li>";
		document.getElementById("barrel-angle").style.background = "yellow";
		counter = 1;
	}
	else if(!isInteger(getBarrelAngle())){
		strMsg += "<li>Barrel Angle must be an integer.</li>";
		document.getElementById("barrel-angle").style.background = "yellow";
		counter = 1;
	}
	else { // in case the background was set earlier
	    document.getElementById("barrel-angle").style.background = "none";
	}
	if(isEmpty(getMuzzleVelocity())){
		strMsg += "<li>Enter muzzle velocity.</li>";
		document.getElementById("muzzle-velocity").style.background = "yellow";
		counter = 2;
	}
	else if(!isInteger(getMuzzleVelocity())){
	    strMsg += "<li>Muzzle Velocity must be an integer.</li>";
		document.getElementById("muzzle-velocity").style.background = "yellow";
		counter = 2;
	}
	else { // in case the background was set earlier
	    document.getElementById("muzzle-velocity").style.background = "none";
	}
	if(isEmpty(getUsername())){
		strMsg += "<li>Enter username.</li>";
		document.getElementById("username").style.background = "yellow";
		counter = 3;
	}
	else if (getUsername().length > 30){
	    strMsg += "<li>Username must be less than 30 characters.</li>";
		document.getElementById("username").style.background = "yellow";
		counter = 3;
	}
	else { // in case the background was set earlier
	    document.getElementById("username").style.background = "none";
	}
	
	/*puts focus in correct input field*/
	switch (counter){
		case 3:
			document.getElementById("username").focus();
			break;	
	    case 2:
			document.getElementById("muzzleVelocity").focus();
			break;
		case 1:
			document.getElementById("barrelAngle").focus();
			break;
	} /*end switch*/
		
    /*checks to see if error occured; if none, brings user to submit page*/	
	if (counter > 0){
		/*displays error messages*/
		p_error_handle.innerHTML = strMsg;
		return false;
	}
	else if (counter == 0)
		return true;			
} /*end validate*/

/*get functions return the value that the user input*/

function getBarrelAngle(){
	return document.getElementById("barrel-angle").value;
}	

function getMuzzleVelocity(){
	return document.getElementById("muzzle-velocity").value;
}

function getUsername(){
	return document.getElementById("username").value;
}

/*function to check for empty string*/
function isEmpty(string){
	return (string=="");
}

/*function to check that the inputted string contains only integers*/
function isInteger(input){
	//looks to find first character that is not a digit
	var pattern = /[^0-9]+/;
	//if such a character is found, will return true so we need the negation to be returned (true if it is an integer)
	return !pattern.test(input);
}
-->
</script>

<title>Howitzer Shooting Practice</title>

<form method="POST" action="index.php" name="fire">
<header>
<h1>Howitzer Shooting Practice</h1>
</header>

<ul id="p_error"></ul>
<fieldset>
    <legend>Initial Settings</legend>
	<p>
	    <label for="shell-weight">Shell Weight (kg):</label>
		<input id="shell-weight" name="shell-weight" readonly="readonly" value="$aHowitzer->shellWeight"/>
	</p>
	<p>
	    <label for="target-distance">Distance to Target (m):</label>
	    <input id="target-distance" name="target-distance" readonly="readonly" value="$aHowitzer->targetDistance"/>
	</p>
	<p class="hidden">
	    <label for="target-length">Length of Target (m):</label>
	    <input id="target-length" name="target-length" readonly="readonly" value="$aHowitzer->targetLength"/>
	</p>
	<p class="hidden">
	    <label for="target-width">Width of Target (m):</label>
	    <input id="target-width" name="target-width" readonly="readonly" value="$aHowitzer->targetWidth"/>
	</p>
	<p>Target Size: $aHowitzer->targetLength m x $aHowitzer->targetWidth m</p>
</fieldset>
<fieldset>
	<legend> Configurable Settings</legend>
	<p>
		<label for="barrel-angle">Angle of Barrel from Ground (&deg;):</label>
		<datalist id="barrel-angles">
		    <select name="barrel-angle">
		        <option value="5"/>
		        <option value="10"/>
		        <option value="15"/>
			    <option value="20"/>
		        <option value="25"/>
		        <option value="30"/>
			    <option value="45"/>
		        <option value="60"/>
		        <option value="75"/>
			    <option value="90"/>
		    </select>
		</datalist>	
		<input id="barrel-angle" name="barrel-angle" list="barrel-angles">
    </p>
    <p>
		<label for="muzzle-velocity">Muzzle Velocity (m/s):</label>
		<datalist id="muzzle-velocities">
		    <select name="muzzle-velocity">
			    <option value="100"/>
			    <option value="125"/>
		        <option value="150"/>
			    <option value="175"/>
		        <option value="200"/>
			    <option value="225"/>
			    <option value="250"/>
			    <option value="275"/>
		        <option value="300"/>
			    <option value="325"/>
		        <option value="350"/>
			    <option value="375"/>
		        <option value="400"/>
			    <option value="425"/>
		        <option value="450"/>
			    <option value="475"/>
		        <option value="500"/>
			    <option value="525"/>
			    <option value="550"/>
			    <option value="575"/>
		        <option value="600"/>
		        <option value="650"/>
			    <option value="700"/>
		        <option value="750"/>
		        <option value="800"/>
			    <option value="850"/>
		        <option value="900"/>
		        <option value="950"/>
		        <option value="1000"/>
		    </select>
		</datalist>	
		<input id="muzzle-velocity" name="muzzle-velocity" list="muzzle-velocities">
    </p>
</fieldset>
<fieldset>
	<legend>Calculate Trajectory</legend>
	<p>
	    <label for="username">Enter Username:</label>
	    <input id="username" name="username">
	</p>
	<input type="submit" class="button" name="submit" value="Fire" onclick="return validate();" />
</fieldset>
</form>
</html>

HTML;
	}
}
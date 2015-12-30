<?php
require_once 'vendor/autoload.php';

use Howitzer\Model\Howitzer;
use Howitzer\Model\UserTrajectoryStats;
use Howitzer\Controller\Controller;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

try {
	$log = new Logger('name');
	$log->pushHandler(new StreamHandler('app.log', Logger::DEBUG));
	
	$connection = new \PDO('mysql:host=localhost;dbname=howitzer;charset=utf8', 'spike', 'tar63t');
	$connection->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
	
	$controller = new Controller(new Howitzer($connection, $log), new UserTrajectoryStats($connection, $log), $log);
	$controller->onLoad();
	
	$connection = null;
} catch (PDOException $e) {
    $log->error($e->getMessage());
    if ($connection != null) {
    	$connection = null;
    }
    /* would work with PHP 5.5
     * } finally {
	if($connection != null) {
    	$connection = null;
    }*/
}

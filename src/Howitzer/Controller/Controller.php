<?php
namespace Howitzer\Controller;

use Howitzer\Model\Howitzer;
use Howitzer\View\PreFireView;
use Howitzer\View\PostFireView;
use Howitzer\View\UserTrajectoryStatsView;

class Controller
{
	private $howitzerModel;
	
	private $userTrajectoryStatsModel;
	
	private $log;

	public function __construct($aHowitzerModel, $aUserTrajectoryStatsModel, $aLog)
	{
		$this->howitzerModel = $aHowitzerModel;
		$this->userTrajectoryStatsModel = $aUserTrajectoryStatsModel;
		$this->log = $aLog;
	}
	
	public function onLoad()
	{
		if (isset($_POST['submit']) && ($_POST['submit'] == "Fire")) {
			// build model
			$this->howitzerModel->build($_POST['shell-weight'], $_POST['target-distance'], $_POST['target-length'], $_POST['target-width'], $_POST['barrel-angle'], $_POST['muzzle-velocity'], $_POST['username']);
			
			// show results UI
			$view = new PostFireView();
			$view->onDisplay($this->howitzerModel);
			
			$this->howitzerModel->storeData();
			
			$constantView = new UserTrajectoryStatsView();
			$constantView->onDisplay($this->userTrajectoryStatsModel, $_POST['username']);
		} else {
			// show initial UI
			$view = new PreFireView();
			$view->onDisplay($this->howitzerModel->getInitialSettings());
			
			$constantView = new UserTrajectoryStatsView();
			$constantView->onDisplay($this->userTrajectoryStatsModel);
		}
	}
}
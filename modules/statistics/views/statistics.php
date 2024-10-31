<?php
class statisticsViewSln extends viewSln {
	public function getTabContent() {
		frameSln::_()->getModule('templates')->loadJqplot();
		$haveData = false;
		$statsTab = $this->getModule()->getCurrentStatTab();
		frameSln::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		frameSln::_()->addJSVar('admin.'. $this->getCode(), 'slnCurrentStatsTab', $statsTab);
		switch($statsTab) {
			case 'login':
				$requests = $this->getModel()->getGraphLogin();
				break;
			/*case '404':
				$requests = $this->getModel()->getGraph404();
				break;*/
			case 'detailed_login':
				frameSln::_()->getModule('templates')->loadJqGrid();
				frameSln::_()->addJSVar('admin.'. $this->getCode(), 'slnDetailedLoginDataUrl', uriSln::mod('statistics', 'getListForTblDetailedLogin', array('reqType' => 'ajax')));
				$haveData = true;
				break;
			//case 'all':
			default:
				//$requests = $this->getModel()->getGraphAll();
                //$statsTab = 'all';
                $requests = $this->getModel()->getGraphLogin();
				$statsTab = 'login';
				break;
		}
		if(isset($requests)) {
			frameSln::_()->addJSVar('admin.'. $this->getCode(), 'slnStatRequests', $requests);
			$haveData = $requests['graph'] 
				&& !empty($requests['graph']) 
				&& isset($requests['graph'][0]) 
				&& !empty($requests['graph'][0]) 
				&& isset($requests['most_visited_url']['total_requests']) 
				&& !empty($requests['most_visited_url']['total_requests']);
			$this->assign('requests', $requests);
		}
		
		$this->assign('haveData', $haveData);
		$this->assign('currentStatsTab', $statsTab);
		$this->assign('statsTabs', $this->getModule()->getStatTabs());
		
		return parent::getContent('statisticsAdmin');
	}
}

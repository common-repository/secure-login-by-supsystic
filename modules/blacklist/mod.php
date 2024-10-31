<?php
class blacklistSln extends moduleSln {
	public function init() {
		parent::init();
		$this->checkCurrentIpBlock();	// If current IP is in blacklist - it will be blocked
		$this->checkTempBlacklist();
		dispatcherSln::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		dispatcherSln::addFilter('optionsDefine', array($this, 'addOptions'));
		//dispatcherSln::addAction($this->getCode().'BeforeGetListForTbl', array($this, 'beforeGetListForTbl'));
		dispatcherSln::addFilter($this->getCode(). 'BeforeSearchForTbl', array($this, 'modifySearchTbl'));
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Blacklist', SLN_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-ban', 'sort_order' => 40,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addOptions($opts) {
		$opts[ $this->getCode() ] = array(
			'label' => __('Blacklist', SLN_LANG_CODE),
			'opts' => array(
				'last_time_temp_check' => array('label' => __('Last time of temporary blacklist check', SLN_LANG_CODE))
			),
		);
		return $opts;
	}
	public function checkCurrentIpBlock() {
		$ipInBlackList = $this->getModel()->checkIp( utilsSln::getIP() );
		if($ipInBlackList) {
			echo $this->getView()->getBlockedPage();
			exit();
		}
	}
	public function checkTempBlacklist() {
		$lastCheckTime = frameSln::_()->getModule('options')->get('last_time_temp_check');
		if(!$lastCheckTime) {
			$this->getModel()->delFromAllTemp();
			frameSln::_()->getModule('options')->getModel()->save('last_time_temp_check', time());
		} else if((time() - $lastCheckTime) >= 3600) {
			if($this->getModel()->delFromAllTemp())
				frameSln::_()->getModule('options')->getModel()->save('last_time_temp_check', time());
		}
	}
	public function modifySearchTbl($search) {
		if($search && isset($search['is_temp']) && $search['is_temp'] == -1) {
			unset($search['is_temp']);
		}
		return $search;
	}
	/*public function beforeGetListForTbl($model) {
		$search = reqSln::getVar('search', 'get');
		$type = isset($search['type']) ? (int) $search['type'] : false;
		if($type !== false && $type != -1) {
			$model->addWhere(array('is_temp' => $type));
		}
	}*/
}


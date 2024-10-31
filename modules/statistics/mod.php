<?php
class statisticsSln extends moduleSln {
	private $_types = array();
	private $_statTabs = array();
	private $_lastStatId = 0;
	public function init() {
		parent::init();
		dispatcherSln::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		add_action('init', array($this, 'addStat'));
		add_action('wp', array($this, 'check404'), 99);
		//add_filter('wp_authenticate_user', array($this, 'checkSubmitLogin'), 99);
		add_filter('login_redirect', array($this, 'submitLoginFailed'), 99, 3);
		dispatcherSln::addAction($this->getCode().'BeforeGetListForTbl', array($this, 'beforeGetListForTbl'));
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Statistics', SLN_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-bar-chart', 'sort_order' => 70,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addStat() {
		if(!is_admin() && !frameSln::_()->getModule('user')->isAdmin()) {
			$this->getTypes();
			$currentType = 0;
			if(frameSln::_()->getModule('pages')->isLogin()) {
				$currentType = $this->_types['login']['id'];
			}
			$this->_lastStatId = $this->getModel()->insert(array(
				'ip' => utilsSln::getIP(),
				'type' => $currentType,
				'url' => uriSln::getFullUrl(),
			));
		}
	}
	public function check404() {
		if($this->_lastStatId && is_404()) {
			$this->getModel()->updateType($this->_lastStatId, $this->_types['404']['id']);
		}
	}
	/*public function checkSubmitLogin($user) {
		if($this->_lastStatId) {
			$currentType = is_wp_error($user) ? $this->_types['login_error']['id'] : $this->_types['login_submit']['id'];
			$this->getModel()->updateType($this->_lastStatId, $currentType);
		}
		return $user;
	}*/
	public function submitLoginFailed($redirect_to, $requested_redirect_to, $user) {
		if($this->_lastStatId) {
			$currentType = is_wp_error($user) ? $this->_types['login_error']['id'] : $this->_types['login_submit']['id'];
			$this->getModel()->updateType($this->_lastStatId, $currentType);
		}
		if($user && !is_wp_error($user) && is_super_admin( $user->ID )) {
			$this->getModel('detailed_login_stat')->insert(array(
				'uid' => $user->ID,
				'ip' => utilsSln::getIP(),
			));
		}
		return $redirect_to;
	}
	public function getTypes() {
		if(empty($this->_types)) {
			$this->_types = array(
				'normal' => array('id' => 1),
				'404' => array('id' => 2),
				'login' => array('id' => 3),
				'login_submit' => array('id' => 4),
				'login_error' => array('id' => 5),
			);
		}
		return $this->_types;
	}
	public function getStatTabs() {
		if(empty($this->_statTabs)) {
			$statTabUrl = frameSln::_()->getModule('options')->getTabUrl('statistics');
			$this->_statTabs = array(
				'login' => array('label' => __('Login page', SLN_LANG_CODE)),
				'detailed_login' => array('label' => __('Admins Login', SLN_LANG_CODE)),
			);
			foreach($this->_statTabs as $k => $v) {
				$this->_statTabs[ $k ]['url'] = $statTabUrl. '&stats_tab='. $k;
			}
		}
		return $this->_statTabs;
	}
	public function getTypeId($code) {
		$this->getTypes();
		return isset($this->_types[ $code ]) ? $this->_types[ $code ]['id'] : false;
	}
	public function getCurrentStatTab() {
		$statsTab = reqSln::getVar('stats_tab', 'get');
		if(empty($statsTab))
			$statsTab = 'login';
		return $statsTab;
	}
	public function beforeGetListForTbl($model) {
		$search = reqSln::getVar('search', 'get');
		$dateFrom = isset($search['dateFrom']) ? $search['dateFrom'] : false;
		$dateTo = isset($search['dateTo']) ? $search['dateTo'] : false;
		if(!empty($dateFrom) && !empty($dateTo)) {
			$model->addWhere(array('additionalCondition' => 'DATE(date_created) >= "'.$dateFrom.'" AND DATE(date_created) <= "'.$dateTo.'"'));
		} elseif(!empty($dateFrom) && empty($dateTo)) {
			$model->addWhere(array('additionalCondition' => 'DATE(date_created) >= "'.$dateFrom.'"'));
		} elseif(empty($dateFrom) && !empty($dateTo)) {
			$model->addWhere(array('additionalCondition' => 'DATE(date_created) <= "'.$dateTo.'"'));
		}
	}
}
<?php
class userSln extends moduleSln {
	protected $_data = array();
    protected $_curentID = 0;
	protected $_dataLoaded = false;
	
    public function loadUserData() {
        return $this->getCurrent();
    }
    public function isAdmin() {
		if(!function_exists('wp_get_current_user')) {
			frameSln::_()->loadPlugins();
		}
        return current_user_can( frameSln::_()->getModule('adminmenu')->getMainCap() );
    }
	public function getCurrentUserPosition() {
		if($this->isAdmin())
			return SLN_ADMIN;
		else if($this->getCurrentID())
			return SLN_LOGGED;
		else 
			return SLN_GUEST;
	}
    public function getCurrent() {
		return wp_get_current_user();
    }
	
    public function getCurrentID() {
		$this->_loadUserData();
		return $this->_curentID;
    }
	protected function _loadUserData() {
		if(!$this->_dataLoaded) {
			if(!function_exists('wp_get_current_user')) frameSln::_()->loadPlugins();
			$user = wp_get_current_user();
			$this->_data = $user->data;
			$this->_curentID = $this->_data->ID;
			$this->_dataLoaded = true;
		}
	}
	public function getAdminsList() {
		global $wpdb;
		$admins = dbSln::get('SELECT * FROM #__users
			INNER JOIN #__usermeta ON #__users.ID = #__usermeta.user_id
			WHERE #__usermeta.meta_key = "#__capabilities" AND #__usermeta.meta_value LIKE "%administrator%"');
		return $admins;
	}
}


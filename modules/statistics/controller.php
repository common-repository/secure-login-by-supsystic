<?php
class statisticsControllerSln extends controllerSln {
	private $_loadModelName = '';
	public function clear() {
		$res = new responseSln();
		$tab = reqSln::getVar('tab', 'post');
		if($this->getModel()->clear( $tab )) {
			// Do nothing for now
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	protected function _prepareListForTbl($data) {
		if($this->_loadModelName == 'detailed_login_stat') {
			if(!empty($data)) {
				$users = array();
				foreach($data as $i => $v) {
					if(!isset($users[ $v['uid'] ])) {
						$users[ $v['uid'] ] = get_userdata( $v['uid'] );
					}
					$data[ $i ]['email'] = $users[ $v['uid'] ]->user_email;
					$blModel = frameSln::_()->getModule('blacklist')->getModel('blacklist');
					if($blModel->checkIpInBlacklist($data[ $i ]['ip'])) {
						if($blModel->getBlacklistType($data[ $i ]['ip']) == "temporary") {
							$data[ $i ]['act'] = "<center>Temporarily blocked</center>";
						} else {
							$data[ $i ]['act'] = "<center>Permanently locked</center>";
						}
					} else {
						$buttons = '<button class="button button-primary" onclick="addToBlacklist('.'\''.$data[ $i ]['ip'].'\''.', '.'\'temporary\''.', this); return false;"><i class="fa fa-fw fa-plus"></i>Temporary Block</button>'.' '.
							       '<button class="button button-primary" onclick="addToBlacklist('.'\''.$data[ $i ]['ip'].'\''.', '.'\'permanent\''.', this); return false;"><i class="fa fa-fw fa-plus"></i>Permanent Block</button>';
						$data[ $i ]['act'] = $buttons;
					}
				}
			}
		}
		return $data;
	}
	protected function _prepareSortOrder($sortOrder) {
		if($this->_loadModelName == 'detailed_login_stat') {
			switch($sortOrder) {
				case 'email':
					$sortOrder = 'uid';
					break;
			}
		}
		return $sortOrder;
	}
	public function getListForTblDetailedLogin() {
		$this->_loadModelName = 'detailed_login_stat';
		parent::getListForTbl();
	}
	public function getModel($name = '') {
		return parent::getModel($name ? $name : $this->_loadModelName);
	}
	public function getPermissions() {
		return array(
			SLN_USERLEVELS => array(
				SLN_ADMIN => array('clear', 'getListForTblDetailedLogin')
			),
		);
	}
}

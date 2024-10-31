<?php
class blacklistControllerSln extends controllerSln {
	protected function _prepareListForTbl($data) {
		if(!empty($data)) {
			foreach($data as $i => $v) {
				$data[ $i ]['is_temp_label'] = empty( $v['is_temp'] ) ? __('Permanent', SLN_LANG_CODE) : __('Temp', SLN_LANG_CODE);
				$data[ $i ]['action'] = '<button href="#" onclick="slnBlacklistRemoveRow('. $data[ $i ]['id']. ', this); return false;" title="'. __('Remove', SLN_LANG_CODE). '" class="button"><i class="fa fa-fw fa-2x fa-trash-o" style="margin-top: 5px;"></i></button>';
			}
		}
		return $data;
	}
	protected function _prepareTextLikeSearch($val) {
		$query = '(ip LIKE "%'. $val. '%"';
		if(is_numeric($val)) {
			$query .= ' OR id LIKE "%'. (int) $val. '%"';
		}
		$query .= ')';
		return $query;
	}
	protected function _prepareSortOrder($sortOrder) {
		switch($sortOrder) {
			case 'type_label':
				$sortOrder = 'type';
				break;
		}
		return $sortOrder;
	}
	public function remove() {
		$res = new responseSln();
		if($this->getModel()->remove(reqSln::getVar('id', 'post'))) {
			$res->addMessage(__('Done', SLN_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			SLN_USERLEVELS => array(
				SLN_ADMIN => array('getListForTbl', 'remove', 'removeGroup', 'clear', 'addToBlacklist', 'addByIpToGroup', 'addByCountryToGroup', 'addByBrowserToGroup')
			),
		);
	}
	public function addToBlacklist() {
		$res = new responseSln();
		if($this->getModel()->save(reqSln::get('post'))) {
			$res->addMessage(__('Done', SLN_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function addByIpToGroup() {
		$res = new responseSln();
		$ips = reqSln::getVar('ips', 'post');
		$blType = reqSln::getVar('type', 'post');
		if(($addedNum = $this->getModel()->addByIpToGroup($ips, $blType))) {
			$res->addMessage(sprintf(__('%d IPs added', SLN_LANG_CODE), $addedNum));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function addByCountryToGroup() {
		$res = new responseSln();
		$countries = reqSln::getVar('country_ids', 'post');
		$blType = reqSln::getVar('type', 'post');
		if(($addedNum = $this->getModel()->addByCountryToGroup($countries, $blType)) !== false) {
			if($addedNum) {
				$res->addMessage(sprintf(__('%d Countries added', SLN_LANG_CODE), $addedNum));
			} else {
				$res->addMessage(__('All Countries was removed from blacklist', SLN_LANG_CODE));
			}
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function addByBrowserToGroup() {
		$res = new responseSln();
		$browserNames = reqSln::getVar('browser_names', 'post');
		$type = reqSln::getVar('type', 'post');
		if(($addedNum = $this->getModel()->addByBrowserToGroup($browserNames, $type)) !== false) {
			if($addedNum) {
				$res->addMessage(sprintf(__('%d Browsers added', SLN_LANG_CODE), $addedNum));
			} else {
				$res->addMessage(__('All Browsers was removed from blacklist', SLN_LANG_CODE));
			}
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
}
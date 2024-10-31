<?php
class optionsControllerSln extends controllerSln {
	public function saveGroup() {
		$res = new responseSln();
		if($this->getModel()->saveGroup(reqSln::get('post'))) {
			$res->addMessage(__('Done', SLN_LANG_CODE));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			SLN_USERLEVELS => array(
				SLN_ADMIN => array('saveGroup')
			),
		);
	}
}


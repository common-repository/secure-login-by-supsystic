<?php
class mailViewSln extends viewSln {
	public function getTabContent() {
		frameSln::_()->getModule('templates')->loadJqueryUi();
		frameSln::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		
		$this->assign('options', frameSln::_()->getModule('options')->getCatOpts( $this->getCode() ));
		$this->assign('testEmail', frameSln::_()->getModule('options')->get('notify_email'));
		return parent::getContent('mailAdmin');
	}
}

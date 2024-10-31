<?php
class mailControllerSln extends controllerSln {
	public function testEmail() {
		$res = new responseSln();
		$email = reqSln::getVar('test_email', 'post');
		if($this->getModel()->testEmail($email)) {
			$res->addMessage(__('Now check your email inbox / spam folders for test mail.'));
		} else 
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function saveMailTestRes() {
		$res = new responseSln();
		$result = (int) reqSln::getVar('result', 'post');
		frameSln::_()->getModule('options')->getModel()->save('mail_function_work', $result);
		$res->ajaxExec();
	}
	public function saveOptions() {
		$res = new responseSln();
		$optsModel = frameSln::_()->getModule('options')->getModel();
		$submitData = reqSln::get('post');
		if($optsModel->saveGroup($submitData)) {
			$res->addMessage(__('Done', SLN_LANG_CODE));
		} else
			$res->pushError ($optsModel->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			SLN_USERLEVELS => array(
				SLN_ADMIN => array('testEmail', 'saveMailTestRes', 'saveOptions')
			),
		);
	}
}

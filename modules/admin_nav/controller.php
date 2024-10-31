<?php
class admin_navControllerSln extends controllerSln {
	public function getPermissions() {
		return array(
			SLN_USERLEVELS => array(
				SLN_ADMIN => array()
			),
		);
	}
}
<?php
class admin_navViewSln extends viewSln {
	public function getBreadcrumbs() {
		$this->assign('breadcrumbsList', $this->getModule()->getBreadcrumbsList());
		return parent::getContent('adminNavBreadcrumbs');
	}
}

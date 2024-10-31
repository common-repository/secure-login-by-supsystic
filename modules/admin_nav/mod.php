<?php
class admin_navSln extends moduleSln {
	public function getBreadcrumbsList() {
		$res = array(
			array('label' => SLN_WP_PLUGIN_NAME, 'url' => frameSln::_()->getModule('adminmenu')->getMainLink()),
		);
		// Try to get current tab breadcrumb
		$activeTab = frameSln::_()->getModule('options')->getActiveTab();
		if(!empty($activeTab) && $activeTab != 'main_page') {
			$tabs = frameSln::_()->getModule('options')->getTabs();
			if(!empty($tabs) && isset($tabs[ $activeTab ])) {
				$res[] = array(
					'label' => $tabs[ $activeTab ]['label'], 'url' => $tabs[ $activeTab ]['url'],
				);
				if($activeTab == 'statistics') {
					$statTabs = frameSln::_()->getModule('statistics')->getStatTabs();
					$currentStatTab = frameSln::_()->getModule('statistics')->getCurrentStatTab();
					if(isset($statTabs[ $currentStatTab ])) {
						$res[] = array(
							'label' => $statTabs[ $currentStatTab ]['label'], 'url' => $statTabs[ $currentStatTab ]['url'],
						);
					}
				}
			}
		}
		return $res;
	}
}


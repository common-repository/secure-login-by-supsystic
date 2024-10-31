<?php
class blacklistViewSln extends viewSln {
	public function getTabContent() {
		frameSln::_()->getModule('templates')->loadJqGrid();

		frameSln::_()->addScript('admin.blacklist', $this->getModule()->getModPath(). 'js/admin.blacklist.js');
		frameSln::_()->addJSVar('admin.blacklist', 'slnBlacklistDataUrl', uriSln::mod('blacklist', 'getListForTbl', array('reqType' => 'ajax')));
		
		frameSln::_()->addStyle('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'css/admin.'. $this->getCode(). '.css');

		$typesForSelect = $this->getModel()->getTypesLabels();

		$permBlockedCounties = $this->getModel()->getBlockedCountryIds("permanent");
		$permBlockedBrowsers = $this->getModel()->getBlockedBrowsersNames("permanent");

		$tempBlockedCounties = $this->getModel()->getBlockedCountryIds("temporary");
		$tempBlockedBrowsers = $this->getModel()->getBlockedBrowsersNames("temporary");
		
		$search = reqSln::getVar('search');
		
		$allCountries = frameSln::_()->getTable('countries')->get('*');
		$countriesForSelect = array();
		foreach($allCountries as $c) {
			$countriesForSelect[ $c['id'] ] = $c['name'];
		}
		$this->assign('currentIp', utilsSln::getIP());
		$this->assign('typesForSelect', $typesForSelect);
		$this->assign('typeSelected', (!empty($search) && isset($search['type']) && !empty($search['type']) ? $search['type'] : '-1'));
		
		$this->assign('countryList', $countriesForSelect);

		$this->assign('permBlockedCounties', $permBlockedCounties);
		$this->assign('permBlockedBrowsers', $permBlockedBrowsers);

		$this->assign('tempBlockedCounties', $tempBlockedCounties);
		$this->assign('tempBlockedBrowsers', $tempBlockedBrowsers);

		$this->assign('currentCountry', $this->getModel()->getCountryCode());
		
		$this->assign('browsersList', utilsSln::getBrowsersList());
		$this->assign('currentBrowser', utilsSln::getBrowser());
		
		return parent::getContent('blacklistAdmin');
	}
	public function getBlockedPage() {
		return parent::getContent('blacklistBlockedPage');
	}
}

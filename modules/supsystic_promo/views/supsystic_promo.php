<?php
class supsystic_promoViewSln extends viewSln {
    public function displayAdminFooter() {
        parent::display('adminFooter');
    }
	public function showWelcomePage() {
		$this->assign('askOptions', array(
			1 => array('label' => 'Google'),
			2 => array('label' => 'Worslness.org'),
			3 => array('label' => 'Refer a friend'),
			4 => array('label' => 'Find on the web'),
			5 => array('label' => 'Other way...'),
		));
		$this->assign('originalPage', uriSln::getFullUrl());
		parent::display('welcomePage');
	}
	public function getOverviewTabContent() {
		frameSln::_()->getModule('templates')->loadJqueryUi();
		frameSln::_()->addScript('jquery.slimscroll', SLN_JS_PATH. 'jquery.slimscroll.js');
		frameSln::_()->addScript('admin.overview', $this->getModule()->getModPath(). 'js/admin.overview.js');
		frameSln::_()->addStyle('admin.overview', $this->getModule()->getModPath(). 'css/admin.overview.css');
		$this->assign('mainLink', $this->getModule()->getMainLink());
		$this->assign('faqList', $this->getFaqList());
		$this->assign('serverSettings', $this->getServerSettings());
		$this->assign('news', $this->getNewsContent());
		$this->assign('contactFields', $this->getModule()->getContactFormFields());
		return parent::getContent('overviewTabContent');
	}
	public function getFaqList() {
		return array(
			__('How to get PRO version of plugin for FREE?', SLN_LANG_CODE) => sprintf(__('You have an incredible opportunity to get PRO version for free. Make Translation of plugin! It will be amazing if you take advantage of this offer! More info you can find here <a target="_blank" href="%s">"Get PRO version of any plugin for FREE"</a>', SLN_LANG_CODE), $this->getModule()->getMainLink()),
			__('Translation', SLN_LANG_CODE) => sprintf(__('All available languages are provided with the Supsystic Secure Login plugin. If your language isn\'t available, your plugin will be in English by default.<br /><b>Available Translations: English</b><br />Translate or update a translation Secure Login WordPress plugin in your language and get a Premium license for FREE. <a target="_blank" href="%s">Contact us</a>.', SLN_LANG_CODE), $this->getModule()->getMainLink(). '#contact'),
		);
	}
	public function getNewsContent() {
		$getData = wp_remote_get('http://supsystic.com/?supsystic_site_news=give_it_for_me_pls');
		$content = '';
		if($getData 
			&& is_array($getData) 
			&& isset($getData['response']) 
			&& isset($getData['response']['code']) 
			&& $getData['response']['code'] == 200
			&& isset($getData['body'])
			&& !empty($getData['body'])
		) {
			$content = $getData['body'];
		} else {
			$content = sprintf(__('There were some problem while trying to retrive our news, but you can always check all list <a target="_blank" href="%s">here</a>.', SLN_LANG_CODE), 'http://supsystic.com/news');
		}
		return $content;
	}
	public function getServerSettings() {
		return array(
			'Operating System' => array('value' => PHP_OS),
            'PHP Version' => array('value' => PHP_VERSION),
            'Server Software' => array('value' => $_SERVER['SERVER_SOFTWARE']),
            'MySQL' => array('value' => mysql_get_server_info()),
            'PHP Safe Mode' => array('value' => ini_get('safe_mode') ? __('Yes', SLN_LANG_CODE) : __('No', SLN_LANG_CODE), 'error' => ini_get('safe_mode')),
            'PHP Allow URL Fopen' => array('value' => ini_get('allow_url_fopen') ? __('Yes', SLN_LANG_CODE) : __('No', SLN_LANG_CODE)),
            'PHP Memory Limit' => array('value' => ini_get('memory_limit')),
            'PHP Max Post Size' => array('value' => ini_get('post_max_size')),
            'PHP Max Upload Filesize' => array('value' => ini_get('upload_max_filesize')),
            'PHP Max Script Execute Time' => array('value' => ini_get('max_execution_time')),
            'PHP EXIF Support' => array('value' => extension_loaded('exif') ? __('Yes', SLN_LANG_CODE) : __('No', SLN_LANG_CODE)),
            'PHP EXIF Version' => array('value' => phpversion('exif')),
            'PHP XML Support' => array('value' => extension_loaded('libxml') ? __('Yes', SLN_LANG_CODE) : __('No', SLN_LANG_CODE), 'error' => !extension_loaded('libxml')),
            'PHP CURL Support' => array('value' => extension_loaded('curl') ? __('Yes', SLN_LANG_CODE) : __('No', SLN_LANG_CODE), 'error' => !extension_loaded('curl')),
		);
	}
}

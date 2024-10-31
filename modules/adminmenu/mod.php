<?php
class adminmenuSln extends moduleSln {
	protected $_mainSlug = 'securelogin-wp-supsystic';
	private $_mainCap = 'manage_options';
    public function init() {
        parent::init();
		add_action('admin_menu', array($this, 'initMenu'), 9);
		$plugName = plugin_basename(SLN_DIR. SLN_MAIN_FILE);
		add_filter('plugin_action_links_'. $plugName, array($this, 'addSettingsLinkForPlug') );
    }
	public function addSettingsLinkForPlug($links) {
		array_unshift($links, '<a href="'. $this->getMainLink(). '">'. __('Settings', SLN_LANG_CODE). '</a>');
		return $links;
	}
	public function initMenu() {
		$mainCap = $this->getMainCap();
		$mainSlug = dispatcherSln::applyFilters('adminMenuMainSlug', $this->_mainSlug);
		$mainMenuPageOptions = array(
			'page_title' => SLN_WP_PLUGIN_NAME,
			'menu_title' => SLN_WP_PLUGIN_NAME,
			'capability' => $mainCap,
			'menu_slug' => $mainSlug,
			'function' => array(frameSln::_()->getModule('options'), 'getAdminPage'));
		$mainMenuPageOptions = dispatcherSln::applyFilters('adminMenuMainOption', $mainMenuPageOptions);
        add_menu_page($mainMenuPageOptions['page_title'], $mainMenuPageOptions['menu_title'], $mainMenuPageOptions['capability'], $mainMenuPageOptions['menu_slug'], $mainMenuPageOptions['function'], 'dashicons-shield-alt');

		$tabs = frameSln::_()->getModule('options')->getTabs();
		$subMenus = array();
		foreach($tabs as $tKey => $tab) {
			if($tKey == 'main_page') continue;	// Top level menu item - is main page, avoid place it 2 times
			$subMenus[] = array(
				'title' => $tab['label'], 'capability' => $mainCap, 'menu_slug' => 'admin.php?page='. $mainSlug. '&tab='. $tKey, 'function' => ''
			);
		}
		$subMenus = dispatcherSln::applyFilters('adminMenuOptions', $subMenus);
		foreach($subMenus as $opt) {
			add_submenu_page($mainSlug, $opt['title'], $opt['title'], $opt['capability'], $opt['menu_slug'], $opt['function']);
		}
	}
	public function getMainLink() {
		return uriSln::_(array('baseUrl' => admin_url('admin.php'), 'page' => $this->getMainSlug()));
	}
	public function getMainSlug() {
		return $this->_mainSlug;
	}
	public function getMainCap() {
		return $this->_mainCap;
	}
}


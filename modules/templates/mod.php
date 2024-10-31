<?php
class templatesSln extends moduleSln {
    protected $_styles = array();
    public function init() {
        if (is_admin() && ($isAdminPlugOptsPage = frameSln::_()->isAdminPlugOptsPage())) {
			$this->loadCoreJs();
			$this->loadAdminCoreJs();
			$this->loadCoreCss();
			$this->loadAdminCoreCss();
			$this->loadChosenSelects();
			frameSln::_()->addScript('adminOptionsSln', SLN_JS_PATH. 'admin.options.js', array(), false, true);
			//add_action('admin_enqueue_scripts', array($this, 'loadMediaScripts'));
		}
        parent::init();
    }
	public function loadMediaScripts() {
		wp_enqueue_media();
	}
	public function loadAdminCoreJs() {
		frameSln::_()->addScript('jquery-ui-dialog');
		frameSln::_()->addScript('tooltipster', SLN_JS_PATH. 'jquery.tooltipster.min.js');
		frameSln::_()->addScript('icheck', SLN_JS_PATH. 'icheck.min.js');
	}
	public function loadCoreJs() {
		frameSln::_()->addScript('jquery');

		frameSln::_()->addScript('commonSln', SLN_JS_PATH. 'common.js');
		frameSln::_()->addScript('coreSln', SLN_JS_PATH. 'core.js');

		$ajaxurl = admin_url('admin-ajax.php');
		$jsData = array(
			'siteUrl'					=> SLN_SITE_URL,
			'imgPath'					=> SLN_IMG_PATH,
			'cssPath'					=> SLN_CSS_PATH,
			'loader'					=> SLN_LOADER_IMG,
			'close'						=> SLN_IMG_PATH. 'cross.gif',
			'ajaxurl'					=> $ajaxurl,
			'options'					=> frameSln::_()->getModule('options')->getAllowedPublicOptions(),
			'SLN_CODE'					=> SLN_CODE,
			'ball_loader'				=> SLN_IMG_PATH. 'ajax-loader-ball.gif',
			'ok_icon'					=> SLN_IMG_PATH. 'ok-icon.png',
		);
		//$jsData['allCheckRegPlugs']	= modInstallerSln::getCheckRegPlugs();

		$jsData = dispatcherSln::applyFilters('jsInitVariables', $jsData);
		frameSln::_()->addJSVar('coreSln', 'SLN_DATA', $jsData);
	}
	public function loadAdminCoreCss() {
		$this->_addStylesArr(array(
			'dashicons'			=> array('for' => 'admin'),
			'tooltipster'		=> array('path' => SLN_CSS_PATH. 'tooltipster.css', 'for' => 'admin'),
			'icheck'			=> array('path' => SLN_CSS_PATH. 'jquery.icheck.css', 'for' => 'admin'),
		));
		$this->loadFontAwesome();
	}
	public function loadCoreCss() {
		$this->_addStylesArr(array(
			'styleSln'				=> array('path' => SLN_CSS_PATH. 'style.css', 'for' => 'admin'),
			'supsystic-uiSln'		=> array('path' => SLN_CSS_PATH. 'supsystic-ui.css', 'for' => 'admin'),
			'bootstrap-alerts'		=> array('path' => SLN_CSS_PATH. 'bootstrap-alerts.css', 'for' => 'admin'),
			'bootstrap-panels'  	=> array('path' => SLN_CSS_PATH. 'bootstrap-panels.css', 'for' => 'admin'),
			'bootstrap-row-cols'	=> array('path' => SLN_CSS_PATH. 'bootstrap-row-cols.css', 'for' => 'admin')
		));
	}
	private function _addStylesArr( $addStyles ) {
		foreach($addStyles as $s => $sInfo) {
			if(!empty($sInfo['path'])) {
				frameSln::_()->addStyle($s, $sInfo['path']);
			} else {
				frameSln::_()->addStyle($s);
			}
		}
	}
	public function loadJqueryUi() {
		frameSln::_()->addStyle('jquery-ui', SLN_CSS_PATH. 'jquery-ui.min.css');
		frameSln::_()->addStyle('jquery-ui.structure', SLN_CSS_PATH. 'jquery-ui.structure.min.css');
		frameSln::_()->addStyle('jquery-ui.theme', SLN_CSS_PATH. 'jquery-ui.theme.min.css');
		frameSln::_()->addScript('jquery.ui', SLN_JS_PATH. 'jquery-ui-1.11.4.min.js');
	}
	public function loadJqGrid() {
		$this->loadJqueryUi();
		frameSln::_()->addScript('jq-grid', SLN_JS_PATH. 'jquery.jqGrid.min.js');
		frameSln::_()->addStyle('jq-grid', SLN_CSS_PATH. 'ui.jqgrid.css');
		$langToLoad = strlen(SLN_WPLANG) > 2 ? substr(SLN_WPLANG, 0, 2) : SLN_WPLANG;
		if(!file_exists(SLN_JS_DIR. 'i18n'. DS. 'grid.locale-'. $langToLoad. '.js')) {
			$langToLoad = 'en';
		}
		frameSln::_()->addScript('jq-grid-lang', SLN_JS_PATH. 'i18n/grid.locale-'. $langToLoad. '.js');
	}
	public function loadFontAwesome() {
		frameSln::_()->addStyle('font-awesomeSln', SLN_CSS_PATH. 'font-awesome.css');
	}
	public function loadChosenSelects() {
		frameSln::_()->addStyle('jquery.chosen', SLN_CSS_PATH. 'chosen.min.css');
		frameSln::_()->addScript('jquery.chosen', SLN_JS_PATH. 'chosen.jquery.min.js');
	}
	public function loadJqplot() {
		$jqplotDir = 'jqplot/';
		
		frameSln::_()->addStyle('jquery.jqplot', SLN_CSS_PATH. 'jquery.jqplot.min.css');
		
		frameSln::_()->addScript('jplot', SLN_JS_PATH. $jqplotDir. 'jquery.jqplot.min.js');
		frameSln::_()->addScript('jqplot.canvasAxisLabelRenderer', SLN_JS_PATH. $jqplotDir. 'jqplot.canvasAxisLabelRenderer.min.js');
		frameSln::_()->addScript('jqplot.canvasTextRenderer', SLN_JS_PATH. $jqplotDir. 'jqplot.canvasTextRenderer.min.js');
		frameSln::_()->addScript('jqplot.dateAxisRenderer', SLN_JS_PATH. $jqplotDir. 'jqplot.dateAxisRenderer.min.js');
		frameSln::_()->addScript('jqplot.canvasAxisTickRenderer', SLN_JS_PATH. $jqplotDir. 'jqplot.canvasAxisTickRenderer.min.js');
		frameSln::_()->addScript('jqplot.highlighter', SLN_JS_PATH. $jqplotDir. 'jqplot.highlighter.min.js');
		frameSln::_()->addScript('jqplot.cursor', SLN_JS_PATH. $jqplotDir. 'jqplot.cursor.min.js');
		frameSln::_()->addScript('jqplot.barRenderer', SLN_JS_PATH. $jqplotDir. 'jqplot.barRenderer.min.js');
		frameSln::_()->addScript('jqplot.categoryAxisRenderer', SLN_JS_PATH. $jqplotDir. 'jqplot.categoryAxisRenderer.min.js');
		frameSln::_()->addScript('jqplot.pointLabels', SLN_JS_PATH. $jqplotDir. 'jqplot.pointLabels.min.js');
	}
}

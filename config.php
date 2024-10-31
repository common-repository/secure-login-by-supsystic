<?php
    global $wpdb;
    if (!defined('WPLANG') || WPLANG == '') {
        define('SLN_WPLANG', 'en_GB');
    } else {
        define('SLN_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('SLN_PLUG_NAME', basename(dirname(__FILE__)));
    define('SLN_DIR', WP_PLUGIN_DIR. DS. SLN_PLUG_NAME. DS);
    define('SLN_TPL_DIR', SLN_DIR. 'tpl'. DS);
    define('SLN_CLASSES_DIR', SLN_DIR. 'classes'. DS);
    define('SLN_TABLES_DIR', SLN_CLASSES_DIR. 'tables'. DS);
	define('SLN_HELPERS_DIR', SLN_CLASSES_DIR. 'helpers'. DS);
    define('SLN_LANG_DIR', SLN_DIR. 'lang'. DS);
    define('SLN_IMG_DIR', SLN_DIR. 'img'. DS);
    define('SLN_TEMPLATES_DIR', SLN_DIR. 'templates'. DS);
    define('SLN_MODULES_DIR', SLN_DIR. 'modules'. DS);
    define('SLN_FILES_DIR', SLN_DIR. 'files'. DS);
    define('SLN_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

    define('SLN_SITE_URL', get_bloginfo('wpurl'). '/');
    define('SLN_JS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/js/');
    define('SLN_CSS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/css/');
    define('SLN_IMG_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/img/');
    define('SLN_MODULES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/modules/');
    define('SLN_TEMPLATES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/templates/');
    define('SLN_JS_DIR', SLN_DIR. 'js/');

    define('SLN_URL', SLN_SITE_URL);

    define('SLN_LOADER_IMG', SLN_IMG_PATH. 'loading-cube.gif');
	define('SLN_TIME_FORMAT', 'H:i:s');
    define('SLN_DATE_DL', '/');
    define('SLN_DATE_FORMAT', 'm/d/Y');
    define('SLN_DATE_FORMAT_HIS', 'm/d/Y ('. SLN_TIME_FORMAT. ')');
    define('SLN_DATE_FORMAT_JS', 'mm/dd/yy');
    define('SLN_DATE_FORMAT_CONVERT', '%m/%d/%Y');
    define('SLN_WPDB_PREF', $wpdb->prefix);
    define('SLN_DB_PREF', 'sln_');
    define('SLN_MAIN_FILE', 'sln.php');

    define('SLN_DEFAULT', 'default');
    define('SLN_CURRENT', 'current');
	
	define('SLN_EOL', "\n");
    
    define('SLN_PLUGIN_INSTALLED', true);
    define('SLN_VERSION', '1.1');
    define('SLN_USER', 'user');
    
    define('SLN_CLASS_PREFIX', 'slnc');
    define('SLN_FREE_VERSION', false);
    
    define('SLN_SUCCESS', 'Success');
    define('SLN_FAILED', 'Failed');
	define('SLN_ERRORS', 'slnErrors');
	
	define('SLN_ADMIN',	'admin');
	define('SLN_LOGGED','logged');
	define('SLN_GUEST',	'guest');
	
	define('SLN_ALL',		'all');
	
	define('SLN_METHODS',		'methods');
	define('SLN_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code, unused for now
	 */
	define('SLN_CODE', 'sln');

	define('SLN_LANG_CODE', 'sln_lng');
	/**
	 * Plugin name
	 */
	define('SLN_WP_PLUGIN_NAME', 'Secure Login by Supsystic');
	/**
	 * Custom defined for plugin
	 */


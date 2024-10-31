<?php
/**
 * Plugin Name: Secure Login by Supsystic
 * Plugin URI: http://supsystic.com
 * Description: Secure login with Google captha, login logs and brute force attack protection. Temporary and permanent blacklists for more login security
 * Version: 1.1
 * Author: supsystic.com
 * Author URI: http://supsystic.com
 **/

/**
 * Base config constants and functions
 */
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
/**
 * Connect all required core classes
 */
importClassSln('dbSln');
importClassSln('installerSln');
importClassSln('baseObjectSln');
importClassSln('moduleSln');
importClassSln('modelSln');
importClassSln('viewSln');
importClassSln('controllerSln');
importClassSln('helperSln');
importClassSln('dispatcherSln');
importClassSln('fieldSln');
importClassSln('tableSln');
importClassSln('frameSln');
/**
 * @deprecated since version 1.0.1
 */
importClassSln('langSln');
importClassSln('reqSln');
importClassSln('uriSln');
importClassSln('htmlSln');
importClassSln('responseSln');
importClassSln('fieldAdapterSln');
importClassSln('validatorSln');
importClassSln('errorsSln');
importClassSln('utilsSln');
importClassSln('modInstallerSln');
importClassSln('wpUpdater');
importClassSln('toeWorslnessWidgetSln');
importClassSln('installerDbUpdaterSln');
importClassSln('dateSln');
/**
 * Check plugin version - maybe we need to update database, and check global errors in request
 */
installerSln::update();
errorsSln::init();
/**
 * Start application
 */
frameSln::_()->parseRoute();
frameSln::_()->init();
frameSln::_()->exec();

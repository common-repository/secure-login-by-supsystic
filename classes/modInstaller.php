<?php
class modInstallerSln {
    static private $_current = array();
    /**
     * Install new moduleSln into plugin
     * @param string $module new moduleSln data (@see classes/tables/modules.php)
     * @param string $path path to the main plugin file from what module is installed
     * @return bool true - if install success, else - false
     */
    static public function install($module, $path) {
        $exPlugDest = explode('plugins', $path);
        if(!empty($exPlugDest[1])) {
            $module['ex_plug_dir'] = str_replace(DS, '', $exPlugDest[1]);
        }
        $path = $path. DS. $module['code'];
        if(!empty($module) && !empty($path) && is_dir($path)) {
            if(self::isModule($path)) {
                $filesMoved = false;
                if(empty($module['ex_plug_dir']))
                    $filesMoved = self::moveFiles($module['code'], $path);
                else
                    $filesMoved = true;     //Those modules doesn't need to move their files
                if($filesMoved) {
                    if(frameSln::_()->getTable('modules')->exists($module['code'], 'code')) {
                        frameSln::_()->getTable('modules')->delete(array('code' => $module['code']));
                    }
					if($module['code'] != 'license')
						$module['active'] = 0;
                    frameSln::_()->getTable('modules')->insert($module);
                    self::_runModuleInstall($module);
                    self::_installTables($module);
                    return true;
                    /*if(frameSln::_()->getTable('modules')->insert($module)) {
                        self::_installTables($module);
                        return true;
                    } else {
                        errorsSln::push(__(array('Install', $module['code'], 'failed ['. mysql_error(). ']')), errorsSln::MOD_INSTALL);
                    }*/
                } else {
                    errorsSln::push(__(array('Move files for', $module['code'], 'failed')), errorsSln::MOD_INSTALL);
                }
            } else
                errorsSln::push(__(array($module['code'], 'is not plugin module')), errorsSln::MOD_INSTALL);
        }
        return false;
    }
    static protected function _runModuleInstall($module, $action = 'install') {
        $moduleLocationDir = SLN_MODULES_DIR;
        if(!empty($module['ex_plug_dir']))
            $moduleLocationDir = utilsSln::getPluginDir( $module['ex_plug_dir'] );
        if(is_dir($moduleLocationDir. $module['code'])) {
            importClassSln($module['code'], $moduleLocationDir. $module['code']. DS. 'mod.php');
            $moduleClass = toeGetClassNameSln($module['code']);
            $moduleObj = new $moduleClass($module);
            if($moduleObj) {
                $moduleObj->$action();
            }
        }
    }
    /**
     * Check whether is or no module in given path
     * @param string $path path to the module
     * @return bool true if it is module, else - false
     */
    static public function isModule($path) {
        return true;
    }
    /**
     * Move files to plugin modules directory
     * @param string $code code for module
     * @param string $path path from what module will be moved
     * @return bool is success - true, else - false
     */
    static public function moveFiles($code, $path) {
        if(!is_dir(SLN_MODULES_DIR. $code)) {
            if(mkdir(SLN_MODULES_DIR. $code)) {
                utilsSln::copyDirectories($path, SLN_MODULES_DIR. $code);
                return true;
            } else 
                errorsSln::push(__('Can not create module directory. Try to set permission to '. SLN_MODULES_DIR. ' directory 755 or 777', SLN_LANG_CODE), errorsSln::MOD_INSTALL);
        } else
            return true;
            //errorsSln::push(__(array('Directory', $code, 'already exists')), errorsSln::MOD_INSTALL);
        return false;
    }
    static private function _getPluginLocations() {
        $locations = array();
        $plug = reqSln::getVar('plugin');
        if(empty($plug)) {
            $plug = reqSln::getVar('checked');
            $plug = $plug[0];
        }
        $locations['plugPath'] = plugin_basename( trim( $plug ) );
        $locations['plugDir'] = dirname(WP_PLUGIN_DIR. DS. $locations['plugPath']);
		$locations['plugMainFile'] = WP_PLUGIN_DIR. DS. $locations['plugPath'];
        $locations['xmlPath'] = $locations['plugDir']. DS. 'install.xml';
        return $locations;
    }
    static private function _getModulesFromXml($xmlPath) {
        if($xml = utilsSln::getXml($xmlPath)) {
            if(isset($xml->modules) && isset($xml->modules->mod)) {
                $modules = array();
                $xmlMods = $xml->modules->children();
                foreach($xmlMods->mod as $mod) {
                    $modules[] = $mod;
                }
                if(empty($modules))
                    errorsSln::push(__('No modules were found in XML file', SLN_LANG_CODE), errorsSln::MOD_INSTALL);
                else
                    return $modules;
            } else
                errorsSln::push(__('Invalid XML file', SLN_LANG_CODE), errorsSln::MOD_INSTALL);
        } else
            errorsSln::push(__('No XML file were found', SLN_LANG_CODE), errorsSln::MOD_INSTALL);
        return false;
    }
    /**
     * Check whether modules is installed or not, if not and must be activated - install it
     * @param array $codes array with modules data to store in database
     * @param string $path path to plugin file where modules is stored (__FILE__ for example)
     * @return bool true if check ok, else - false
     */
    static public function check($extPlugName = '') {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
			$modulesData = array();
            foreach($modules as $m) {
                $modDataArr = utilsSln::xmlNodeAttrsToArr($m);
                if(!empty($modDataArr)) {
                    if(frameSln::_()->moduleExists($modDataArr['code'])) { //If module Exists - just activate it
                        self::activate($modDataArr);
                    } else {                                           //  if not - install it
                        if(!self::install($modDataArr, $locations['plugDir'])) {
                            errorsSln::push(__(array('Install', $modDataArr['code'], 'failed')), errorsSln::MOD_INSTALL);
                        }
                    }
					$modulesData[] = $modDataArr;
                }
            }
        } else
            errorsSln::push(__('Error Activate module', SLN_LANG_CODE), errorsSln::MOD_INSTALL);
        if(errorsSln::haveErrors(errorsSln::MOD_INSTALL)) {
            self::displayErrors();
            return false;
        }
		update_option(SLN_CODE. '_full_installed', 1);
        return true;
    }
	/**
	 * We will run this each time plugin start to check modules activation messages
	 */
	static public function checkActivationMessages() {
		// Empty for now
	}
	/** @deprecated since version 1.0.5
	static public function getActivationMessages() {
		return get_option(SLN_CODE. 'activate_modules_msg', array());;
	}*/
    /**
     * Deactivate module after deactivating external plugin
     */
    static public function deactivate() {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsSln::xmlNodeAttrsToArr($m);
                if(frameSln::_()->moduleActive($modDataArr['code'])) { //If module is active - then deacivate it
                    if(frameSln::_()->getModule('options')->getModel('modules')->put(array(
                        'id' => frameSln::_()->getModule($modDataArr['code'])->getID(),
                        'active' => 0,
                    ))->error) {
                        errorsSln::push(__('Error Deactivation module', SLN_LANG_CODE), errorsSln::MOD_INSTALL);
					}
                }
            }
        }
        if(errorsSln::haveErrors(errorsSln::MOD_INSTALL)) {
            self::displayErrors(false);
            return false;
        }
        return true;
    }
    static public function activate($modDataArr) {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsSln::xmlNodeAttrsToArr($m);
                if(!frameSln::_()->moduleActive($modDataArr['code'])) { //If module is not active - then acivate it
                    if(frameSln::_()->getModule('options')->getModel('modules')->put(array(
                        'code' => $modDataArr['code'],
                        'active' => 1,
                    ))->error) {
                        errorsSln::push(__('Error Activating module', SLN_LANG_CODE), errorsSln::MOD_INSTALL);
                    } else {
						self::_runModuleInstall($modDataArr, 'activate');
					}
                }
            }
        }
    } 
    /**
     * Display all errors for module installer, must be used ONLY if You realy need it
     */
    static public function displayErrors($exit = true) {
        $errors = errorsSln::get(errorsSln::MOD_INSTALL);
        foreach($errors as $e) {
            echo '<b style="color: red;">'. $e. '</b><br />';
        }
        if($exit) exit();
    }
    static public function uninstall() {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsSln::xmlNodeAttrsToArr($m);
                self::_uninstallTables($modDataArr);
                frameSln::_()->getModule('options')->getModel('modules')->delete(array('code' => $modDataArr['code']));
                utilsSln::deleteDir(SLN_MODULES_DIR. $modDataArr['code']);
            }
        }
    }
    static protected  function _uninstallTables($module) {
        if(is_dir(SLN_MODULES_DIR. $module['code']. DS. 'tables')) {
            $tableFiles = utilsSln::getFilesList(SLN_MODULES_DIR. $module['code']. DS. 'tables');
            if(!empty($tableNames)) {
                foreach($tableFiles as $file) {
                    $tableName = str_replace('.php', '', $file);
                    if(frameSln::_()->getTable($tableName))
                        frameSln::_()->getTable($tableName)->uninstall();
                }
            }
        }
    }
    static public function _installTables($module, $action = 'install') {
		$modDir = empty($module['ex_plug_dir']) ? 
            SLN_MODULES_DIR. $module['code']. DS :
            utilsSln::getPluginDir($module['ex_plug_dir']). $module['code']. DS;
        if(is_dir($modDir. 'tables')) {
            $tableFiles = utilsSln::getFilesList($modDir. 'tables');
            if(!empty($tableFiles)) {
                frameSln::_()->extractTables($modDir. 'tables'. DS);
                foreach($tableFiles as $file) {
                    $tableName = str_replace('.php', '', $file);
                    if(frameSln::_()->getTable($tableName))
                        frameSln::_()->getTable($tableName)->$action();
                }
            }
        }
    }
}
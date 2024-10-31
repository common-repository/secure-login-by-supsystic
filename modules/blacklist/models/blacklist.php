<?php
class blacklistModelSln extends modelSln {
	private $_types = array();
	private $_typesLabelsById = array();
	public function __construct() {
		$this->_setTbl('blacklist');
	}
	public function save($d = array()) {
		$d['ip'] = isset($d['ip']) ? trim($d['ip']) : '';
		$d['type'] = isset($d['type']) ? trim($d['type']) : '';
		if(isset($d['is_temp'])) {
			$d['is_temp'] = ($d['is_temp'] == "temporary") ? 1 : 0;
		}
		if(isset($d['ip'])) {
			$this->getTypes();
			$typeId = (int) (isset($this->_types[ $d['type'] ]) ? $this->_types[ $d['type'] ]['id'] : $d['type']);
			$insertData = array(
				'ip' => $d['ip'],
				'type' => $typeId,
				'is_temp' => $d['is_temp']
			);
			if(frameSln::_()->getTable('blacklist')->insert($insertData)) {
				return true;
			} else
				$this->pushError(__('Database error detected', SLN_LANG_CODE));
		} else
			$this->pushError(__('Empty IP', SLN_LANG_CODE));
		return false;
	}
	public function getList() {
		return $this->getFromTbl();
	}
	public function getByIp($ip) {
		return $this->getList(array('ip' => $ip));
	}
	public function getListForCountries($type) {
		$this->addWhere(array('is_temp'=>$type));
		return $this->getFromTbl(array('tbl' => 'blacklist_countries'));
	}
	public function getBlockedCountryIds($type) {
		if(!empty($type)) {
			if($type == "permanent") {
				$is_temp = 0;
			} else {
				$is_temp = 1;
			}
		}
		$res = array();
		$countries = $this->getListForCountries($is_temp);
		if(!empty($countries)) {
			foreach($countries as $c) {
				$res[] = $c['country_id'];
			}
		}
		return $res;
	}
	public function getListForBrowsers($type) {
		$this->addWhere(array('is_temp'=>$type));
		return $this->getFromTbl(array('tbl' => 'blacklist_browsers'));
	}
	public function getBlockedBrowsersNames($type) {
		if(!empty($type)) {
			if($type == "permanent") {
				$is_temp = 0;
			} else {
				$is_temp = 1;
			}
		}
		$res = array();
		$browsers = $this->getListForBrowsers($is_temp);
		if(!empty($browsers)) {
			foreach($browsers as $b) {
				$res[] = $b['browser_name'];
			}
		}
		return $res;
	}
	public function checkIp($ip) {
		$ipBlocked = (int) frameSln::_()->getTable('blacklist')->get('COUNT(*) AS total', array('ip' => $ip), '', 'one');
		if(!$ipBlocked) {
			$ipBlocked = $this->checkCountryByIp( $ip );
			if(!$ipBlocked) {
				$ipBlocked = $this->checkBrowser();
			}
		}
		return $ipBlocked;
	}
	public function getCountryCode( $ip = false ) {
		static $sxGeo;
		if(!$sxGeo) {
			importClassSln('SxGeo', SLN_HELPERS_DIR. 'SxGeo.php');
			$sxGeo = new SxGeo(SLN_FILES_DIR. 'SxGeo.dat');
		}
		if(!$ip)
			$ip = utilsSln::getIP ();
		return $sxGeo->getCountry($ip);
	}
	public function checkCountryByIp($ip) {
		$countryBlocked = (int) $this->getCount(array('tbl' => 'blacklist_countries'));
		if($countryBlocked) {
			$countryBlocked = false;
			$countryCode = $this->getCountryCode($ip);
			if(!empty($countryCode)) {
				$countryBlocked = (int) dbSln::get('SELECT COUNT(*) AS total FROM @__blacklist_countries
					INNER JOIN @__countries ON @__countries.id = @__blacklist_countries.country_id
					WHERE @__countries.iso_code_2 = "'. $countryCode. '"', 'one');
			}
		}
		return $countryBlocked;
	}
	public function checkBrowser() {
		$browserBlocked = (int) $this->getCount(array('tbl' => 'blacklist_browsers'));
		if($browserBlocked) {
			$currentBrowser = utilsSln::getBrowser();
			$browserBlocked = (int) $this
					->setWhere(array('browser_name' => $currentBrowser['name']))
					->getCount(array('tbl' => 'blacklist_browsers'));
		}
		return $browserBlocked;
	}
	/*public function getTypes() {
		if(empty($this->_types)) {
			$this->_types = array(
				'login' => array('label' => __('Login', SLN_LANG_CODE), 'id' => 1),
				'404' => array('label' => __('404 page brute force', SLN_LANG_CODE), 'id' => 2),
			);
			
		}
		return $this->_types;
	}*/
	public function getTypes() {
		if(empty($this->_types)) {
			$this->_types = array(
				'all'  => array('label' => __('All types', SLN_LANG_CODE), 'id' => -1),
				'perm' => array('label' => __('Permanent', SLN_LANG_CODE), 'id' => 0),
				'temp' => array('label' => __('Temporary', SLN_LANG_CODE), 'id' => 1),
			);

		}
		return $this->_types;
	}
	public function getTypeLabelById($id) {
		$this->getTypesLabels();
		return isset($this->_typesLabelsById[ $id ]) ? $this->_typesLabelsById[ $id ] : false;
	}
	public function getTypesLabels() {
		$this->getTypes();
		if(empty($this->_typesLabelsById)) {
			foreach($this->_types as $t) {
				$this->_typesLabelsById[ $t['id'] ] = $t['label'];
			}
		}
		return $this->_typesLabelsById;
	}
	public function remove($id) {
		$id = (int) $id;
		if($id) {
			if(frameSln::_()->getTable( $this->_tbl )->delete(array('id' => $id))) {
				return true;
			} else
				$this->pushError (__('Database error detected', SLN_LANG_CODE));
		} else
			$this->pushError(__('Invalid ID', SLN_LANG_CODE));
		return false;
	}
	public function checkIpInBlacklist($ip) {
		$ipBlocked = (int) frameSln::_()->getTable('blacklist')->get('COUNT(*) AS total', array('ip' => $ip), '', 'one');
		if($ipBlocked) {
			return true;
		}
		return false;
	}
	public function getBlacklistType($ip) {
		$type = frameSln::_()->getTable('blacklist')->get('is_temp', array('ip' => $ip), '', 'one');
		if($type == "1") {
			return "temporary";
		} else {
			return "permanent";
		}
	}
	public function addByIpToGroup($ips, $type) {
		if(!empty($ips)) {
			if(!is_array($ips)) {
				$ips = array_map('trim', explode(PHP_EOL, $ips));
			}
			if(!empty($type)) {
				if($type == "permanent") {
					$is_temp = 0;
				} else {
					$is_temp = 1;
				}
			}
			$values = array();
			$invalidIps = array();
			foreach($ips as $ip) {
				if(empty($ip)) continue;
				if(strlen($ip) > 16) {
					$invalidIps[] = $ip;
					continue;
				}
				$values[] = '("'. $ip. '", '.$is_temp.')';
			}
			if(!empty($values) && empty($invalidIps)) {
				if(dbSln::query('INSERT INTO @__'. $this->_tbl. ' (ip, is_temp) VALUES '. implode(',', $values))) {
					return count($values);
				} else
					$this->pushError (__('Database error detected', SLN_LANG_CODE));
			} else {
				if(count($invalidIps)) {
					$this->pushError(sprintf(__('IPs list contains invalid values: %s', SLN_LANG_CODE), implode(', ', $invalidIps)));
				} else
					$this->pushError(__('Empty IPs list provided', SLN_LANG_CODE));
			}
		} else
			$this->pushError(__('Empty IPs list provided', SLN_LANG_CODE));
		return false;
	}
	public function addByCountryToGroup($countryIds, $type) {
		if(!empty($type)) {
			if($type == "permanent") {
				$is_temp = 0;
			} else {
				$is_temp = 1;
			}
		}
		// Clear all prev. countries in current blacklist
		frameSln::_()->getTable('blacklist_countries')->delete(array('is_temp'=>$is_temp));
		if(!empty($countryIds)) {
			if(!is_array($countryIds))
				$countryIds = array( $countryIds );
			$countryIds = array_map('intval', $countryIds);
			$query = 'INSERT INTO @__blacklist_countries (country_id, is_temp) VALUES ';
			for($i=0; $i<count($countryIds); $i++)
			{
				if($i == (count($countryIds) - 1)) {
					$query .= '('.$countryIds[$i].', '.$is_temp.')';
				} else {
					$query .= '('.$countryIds[$i].', '.$is_temp.'), ';
				}
			}
			if(!dbSln::query($query)) {
				$this->pushError(__('Database error detected', SLN_LANG_CODE));
				return false;
			}
			return count($countryIds);
		}
		return 0;	// No one were added - just cleared country list - this is not error, this is ok
	}
	public function addByBrowserToGroup($browserNames, $type) {
		if(!empty($type)) {
			if($type == "permanent") {
				$is_temp = 0;
			} else {
				$is_temp = 1;
			}
		}
		// Clear all prev. browsers in current blacklist
		frameSln::_()->getTable('blacklist_browsers')->delete(array('is_temp'=>$is_temp));
		if(!empty($browserNames)) {
			if(!is_array($browserNames))
				$browserNames = array( $browserNames );
			$query = 'INSERT INTO @__blacklist_browsers (browser_name, is_temp) VALUES ';
			for($i=0; $i<count($browserNames); $i++)
			{
				if($i == (count($browserNames) - 1)) {
					$query .= '("'.$browserNames[$i].'", '.$is_temp.')';
				} else {
					$query .= '("'.$browserNames[$i].'", '.$is_temp.'), ';
				}
			}
			if(!dbSln::query($query)) {
				$this->pushError(__('Database error detected', SLN_LANG_CODE));
				return false;
			}
			return count($browserNames);
		}
		return 0;	// No one were added - just cleared browsers list - this is not error, this is ok
	}
	public function delFromAllTemp() {
		$delIPs = frameSln::_()->getTable('blacklist')->delete(array(
			'is_temp'=>1,
			'additionalCondition'=>'date_created < NOW()'
		));
		$delCountries = frameSln::_()->getTable('blacklist_countries')->delete(array(
			'is_temp'=>1,
			'additionalCondition'=>'date_created < NOW()'
		));
		$delBrowsers = frameSln::_()->getTable('blacklist_browsers')->delete(array(
			'is_temp'=>1,
			'additionalCondition'=>'date_created < NOW()'
		));
		if($delIPs && $delCountries && $delBrowsers) {
			return true;
		} else {
			return false;
		}
	}
}

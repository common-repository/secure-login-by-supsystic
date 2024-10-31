<?php
class secure_loginSln extends moduleSln {
	private $_cookieName = 'sln_email_auth_cookie';
	private $_cookieSecureName = 'sln_email_auth_cookie_secure';
    public $imgPath = '';
    public $imgName = '';
	public $captchaType = '';
	public function init() {
		parent::init();
		dispatcherSln::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		dispatcherSln::addFilter('optionsDefine', array($this, 'addOptions'));
		add_action('login_form', array($this, 'showCapchaOnLogin'));
		add_filter('wp_authenticate_user', array($this, 'checkLoginCapcha'), 99);
		add_filter('wp_authenticate_user', array($this, 'checkAdminIpLogin'), 99);
		add_action('wp_login_failed', array($this, 'addInvalidLoginTry'));
		add_action('user_profile_update_errors', array($this, 'checkPasswordStrength'));
		$this->checkAdminPasswordsChange();	// see option admin_pass_change_enb
		add_action('admin_notices', array($this, 'checkChangePassMsg'));
		add_action('user_profile_update_errors', array($this, 'resetRemoveChangePassMsg'), 99, 3);
		add_filter('login_errors', array($this, 'checkLoginErrorDisable'));
		add_action('plugins_loaded', array($this, 'checkLoginPageRestrict'), 1);
		add_action('clear_auth_cookie', array($this, 'clearCookie'));
		add_action('init', array($this, 'checkEmailAuth'), 99);
		$this->captchaType = frameSln::_()->getModule('options')->get('captcha_type');
		if($this->captchaType == "custom") {
			$this->imgName = 'login-captcha-1.png';
		} else {
			$this->imgName = 'login-captcha-2.png';
		}
        $this->imgPath = $this->getModPath().'img/'.$this->imgName;
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Login Security', SLN_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-key', 'sort_order' => 10,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addOptions($opts) {
		$opts[ $this->getCode() ] = array(
			'label' => __('Login Security', SLN_LANG_CODE),
			'opts' => array(
				'email_auth_enb' => array('label' => __('Email Authentication', SLN_LANG_CODE), 'weight' => 75, 'html' => 'checkboxHiddenVal', 'desc' => __('Enable 2 step authentication via email.', SLN_LANG_CODE)),
				'email_auth_opt_for_roles' => array('label' => __('Email authentication option is available for roles', SLN_LANG_CODE), 'def'=>'all'),
				'email_auth_roles' => array('label' => __('Enable email authentication for roles', SLN_LANG_CODE)),
				'email_auth_crypt_time' => array('label' => __('Timestamp to encrypt cookies', SLN_LANG_CODE)),

				'capcha_on_login' => array('label' => __('Capcha on login', SLN_LANG_CODE), 'weight' => 70, 'html' => 'checkboxHiddenVal', 'desc' => __("CAPTCHA is one of the most effective means against automatic password selection, the so called broot force. Of course, CAPTCHA creates a certain inconvenience for a user that needs to write it down correctly. That’s why we use the verified reCapcha from Google that is complicated for bots and simple for humans.<p><img src={$this->imgPath}>", SLN_LANG_CODE)),
				
				'htaccess_passwd_enable' => array('label' => __('Anti BrootForce second password', SLN_LANG_CODE), 'weight' => 65, 'html' => 'checkboxHiddenVal', 'htaccessChange' => true, 'forBothHtaccess' => true, 'desc' => __('A simple and effective tool that allows you to reduce the probability of guessing a password brute force and at the same time protect the load associated with such an attack.', SLN_LANG_CODE)),
				'htaccess_passwd_content' => array('label' => __('Htaccess password content', SLN_LANG_CODE)),
				
				'login_lockout' => array('label' => __('Login lockout', SLN_LANG_CODE), 'weight' => 60, 'html' => 'checkboxHiddenVal', 'desc' => __('The most common way to hack a site is by selecting the username/password through the login form. To prevent such hacking attack, select the automatic add-on of an IP that was used to pick the username/password for your site to black list, in case such IP has generated several unsuccessful login attempts.', SLN_LANG_CODE)),
				//+++
				'login_lockout_attempts' => array('label' => __('Attempts', SLN_LANG_CODE), 'def' => 3),
				'login_lockout_stop_time' => array('label' => __('Stop time', SLN_LANG_CODE), 'def' => 5),
				'login_lockout_attempts_data' => array('label' => __('Attempts Array', SLN_LANG_CODE), 'def' => array()),
				// enb == enable
				'passwd_min_length_enb' => array('label' => __('Minimal password length', SLN_LANG_CODE), 'weight' => 20, 'html' => 'checkboxHiddenVal', 'desc' => __('A password is the key to your website. By using a combination of letters, numbers and symbols in your password you increase the safety of your account. The default length of a WordPress password is 7 symbols but you can increase this number with the help of this option.', SLN_LANG_CODE)),
				//+++
				'passwd_min_length' => array('label' => __('Min pass length symbols', SLN_LANG_CODE), 'def' => 7),
				
				'admin_ip_login_enb' => array('label' => __('Admin IP login protection', SLN_LANG_CODE), 'weight' => 40, 'html' => 'checkboxHiddenVal', 'desc' => __('Attaching the login permission to any IP is one of the most effective means of protection. However, such protection may cause some inconvenience. Thus, by activating this option you yourself won’t be able to login from another IP.', SLN_LANG_CODE)),
				//+++
				'admin_ip_login_list' => array('label' => __('Admin IP list', SLN_LANG_CODE), 'def' => ''),
				// enb == enable
				'admin_pass_change_enb' => array('label' => __('Regular admin change passwords', SLN_LANG_CODE), 'weight' => 40, 'html' => 'checkboxHiddenVal', 'desc' => __('', SLN_LANG_CODE)),
				//+++
				'admin_pass_change_freq' => array('label' => __('Admin pass change freq', SLN_LANG_CODE), 'def' => '30'),
				'admin_pass_change_auto' => array('label' => __('Do it auto', SLN_LANG_CODE), 'def' => '0'),
				'admin_pass_change_last_check' => array('label' => __('Do it auto', SLN_LANG_CODE), 'def' => '0'),
				// enb == enable
				'hide_login_errors_enb' => array('label' => __('Hide login error messages', SLN_LANG_CODE), 'weight' => 30, 'html' => 'checkboxHiddenVal', 'desc' => __('Will not display errors on login form if login was incorrect.', SLN_LANG_CODE)),
				// enb == enable
				'hide_login_page_enb' => array('label' => __('Hide login page', SLN_LANG_CODE), 'weight' => 30, 'html' => 'checkboxHiddenVal', 'htaccessChange' => true, 'desc' => __('The attacker will not know the address of the page to log in to your site - this will reduce the risk of breaking of site.', SLN_LANG_CODE)),
				'hide_login_page_slug' => array('label' => __('New login slug', SLN_LANG_CODE), 'def' => ''),

				'captcha_type' => array('label' => __('Captcha type', SLN_LANG_CODE), 'def' => 'custom'),
				'recaptcha_sitekey' => array('label' => __('reCaptcha sitekey', SLN_LANG_CODE), 'def' => ''),
				'recaptcha_secret' => array('label' => __('reCaptcha secret', SLN_LANG_CODE), 'def' => ''),
			),
		);
		return $opts;
	}
	public function showCapchaOnLogin() {
		if(frameSln::_()->getModule('options')->get('capcha_on_login')) {
			frameSln::_()->getModule('templates')->loadFontAwesome();
			if($this->captchaType == "custom") {
				echo $this->getView()->getCapchaOnLogin();
			} else {
				echo $this->getView()->getReCaptchaOnLogin();
			}
		}
	}
	public function checkLoginCapcha($user) {
		if(frameSln::_()->getModule('options')->get('capcha_on_login') && !is_wp_error($user)) {
			if($this->captchaType == "custom") {
				if(!$this->recaptchaCheckAnswer($this->getModel()->getCapchaPrivateKey(),
					$_SERVER['REMOTE_ADDR'],
					reqSln::getVar('recaptcha_challenge_field', 'post'),
					reqSln::getVar('recaptcha_response_field', 'post'))
				) {
					$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid words from capcha.', SLN_LANG_CODE));
				}
			} else {
				if(!$this->noCaptchaRecaptchaCheckAnsw($this->getModel()->getCapchaPrivateKey(),
					$_SERVER['REMOTE_ADDR'],
					reqSln::getVar('g-recaptcha-response', 'post')
					)) {
					$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Access denied. Looks like you are robot!', SLN_LANG_CODE));
				}
			}
		}
		return $user;
	}
	public function checkAdminIpLogin($user) {
		if(frameSln::_()->getModule('options')->get('admin_ip_login_enb')
			&& !is_wp_error($user) 
			&& is_super_admin( $user->ID )
		) {
			$ipListStr = frameSln::_()->getModule('options')->get('admin_ip_login_list');
			if($ipListStr) {
				$ipListArr = array_map('trim', explode(SLN_EOL, $ipListStr));
				$currIp = utilsSln::getIP();
				if(!in_array($currIp, $ipListArr)) {
					$user = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: You can not login from this IP.', SLN_LANG_CODE));
				}
			}
		}
		return $user;
	}
	public function recaptchaCheckAnswer ($privkey, $remoteip, $challenge, $response, $extra_params = array()) {
		if ($privkey == null || $privkey == '') {
			return false;
		}
		if ($remoteip == null || $remoteip == '') {
			return false;
		}
		//discard spam submissions
		if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
			return false;
		}
		$response = $this->_recaptchaHttpPost($this->getModel()->getCapchaVerifyServer(), '/recaptcha/api/verify',
			array (
				'privatekey' => $privkey,
				'remoteip' => $remoteip,
				'challenge' => $challenge,
				'response' => $response,
			) + $extra_params
		);
		if(empty($response))	// Empty answer from server - just let it go
			return true;
		$answers = explode ("\n", $response);
		if (trim ($answers [0]) == 'true') {
			return true;
		}
		return false;
	}
	private function _recaptchaHttpPost($host, $path, $data, $port = 80) {
			$req = $this->_recaptchaQsencode ($data);
			$eol = "\r\n";
			$reqData = array(
				"POST $path HTTP/1.0",
				"Host: $host",
				"Content-Type: application/x-www-form-urlencoded",
				"Content-Length: " . strlen($req),
				"User-Agent: reCAPTCHA/PHP",
			);
			$response = '';
			// Usual sock request
			// FIX ===
			if( false === ( $fs = @fsockopen($host, $port, $errno, $errstr, 20) ) ) {
				$httpRequest = implode($eol, $reqData). $eol.$eol. $req;
				fwrite($fs, $httpRequest);
				while ( !feof($fs) )
					$response .= fgets($fs, 1160); // One TCP-IP packet
				fclose($fs);
				$response = explode($eol. $eol, $response, 2);
			} else {	// But if this will not work - try to make wp remove request
				$requestUrl = 'http://'. $host. $path;
				$headers = implode($eol, $reqData);

				$request = new WP_Http;
				$wpRemoteReq = $request->request( $requestUrl , array( 'method' => 'POST', 'body' => $req, 'headers' => $headers ) );
				if($wpRemoteReq && isset($wpRemoteReq['body']) && !empty($wpRemoteReq['body'])) {
					$response = $wpRemoteReq['body'];
				}
			}
			return $response;
	}
	private function _recaptchaQsencode ($data) {
		$req = "";
		foreach ( $data as $key => $value )
			$req .= $key . '=' . urlencode( stripslashes($value) ) . '&';

		// Cut the last '&'
		$req = substr($req, 0, strlen($req)-1);
		return $req;
	}
	public function noCaptchaRecaptchaCheckAnsw($privateKey, $host, $captcha) {
		$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$privateKey."&response=".$captcha."&remoteip=".$host);
		$response = json_decode($response, true);
		if($response['success'] == false) {
			return false;
		} else {
			return true;
		}
	}
	public function addInvalidLoginTry() {
		if(frameSln::_()->getModule('options')->get('login_lockout')) {
			$lockoutAttempts = frameSln::_()->getModule('options')->get('login_lockout_attempts_data');
			if(!$lockoutAttempts)
				$lockoutAttempts = array();
			$ip = utilsSln::getIP();
			$time = time();
			$blocked = false;
			if(!isset($lockoutAttempts[ $ip ])) {
				$lockoutAttempts[ $ip ] = array(
					'attempts' => 1,
					'last_try' => $time,
				);
			} else {
				$stopTime = (int) frameSln::_()->getModule('options')->get('login_lockout_stop_time');
				if(!$stopTime || ($time - $lockoutAttempts[ $ip ]['last_try']) <= $stopTime * 60) {
					$lockoutAttempts[ $ip ]['attempts']++;
				}
				$lockoutAttempts[ $ip ]['last_try'] = $time;
			}
			// Block IP
			if($lockoutAttempts[ $ip ]['attempts'] >= frameSln::_()->getModule('options')->get('login_lockout_attempts')) {
				frameSln::_()->getModule('blacklist')->getModel()->save(array(
					'ip' => $ip, 
					'type' => 'login',
					'is_temp' => 'temporary'));
				$notifyEmail = frameSln::_()->getModule('options')->get('notify_email');
				if(!empty($notifyEmail)) {
					frameSln::_()->getModule('mail')->send(
							$notifyEmail, 
							__('Login form blocked IP', SLN_LANG_CODE),
							sprintf(__('On your site %s login form was added IP %s to blacklist after it tried to login %d times.', SLN_LANG_CODE), SLN_SITE_URL, $ip, $lockoutAttempts[ $ip ]['attempts']));
				}
				unset($lockoutAttempts[ $ip ]);
				$blocked = true;
			}
			frameSln::_()->getModule('options')->getModel()->save('login_lockout_attempts_data', $lockoutAttempts);
			if($blocked) {	// If blocked - just redirect to show block page
				redirect(uriSln::getFullUrl());
			}
		}
	}
	public function checkPasswordStrength($errors) {
		if(frameSln::_()->getModule('options')->get('passwd_min_length_enb')) {
			$pass1 = reqSln::getVar('pass1', 'post');
			$minLength = (int) frameSln::_()->getModule('options')->get('passwd_min_length');
			if(!empty($pass1) && $minLength && strlen($pass1) < $minLength) {
				$errors->add('weak-password', sprintf(__('Password should be at least %d symbols', SLN_LANG_CODE), $minLength), array( 'form-field' => 'pass1' ));
			}
		}
	}
	public function checkAdminPasswordsChange() {
		if(frameSln::_()->getModule('options')->get('admin_pass_change_enb')) {
			$time = time();
			$lastCheck = (int) frameSln::_()->getModule('options')->get('admin_pass_change_last_check');
			if(!$lastCheck) {
				$lastCheck = $time;
				frameSln::_()->getModule('options')->getModel()->save('admin_pass_change_last_check', $time);
			}
			$checkDays = (int) frameSln::_()->getModule('options')->get('admin_pass_change_freq');
			if($checkDays && ($time - $lastCheck) >= $checkDays * 3600 * 24) {
				// We need this to trigger after pluggable functions will be loaded
				add_action('plugins_loaded', array($this, 'makeAdminsPasswordChange'));
			}
		}
	}
	public function makeAdminsPasswordChange() {
		$adminsList = frameSln::_()->getModule('user')->getAdminsList();
		$time = time();
		if(!empty($adminsList)) {
			$autoChange = frameSln::_()->getModule('options')->get('admin_pass_change_auto');
			foreach($adminsList as $admin) {
				if($autoChange && isset($admin['user_email']) && !empty($admin['user_email'])) {
					$newPass = $this->generateNewPass($admin);
					if($newPass) {
						$this->sendNewPass($admin, $newPass);
					}
				} else
					update_user_meta($admin['ID'], SLN_CODE. '_pass_change_require', $time);
			}
		}
		frameSln::_()->getModule('options')->getModel()->save('admin_pass_change_last_check', $time);
	}
	public function generateNewPass($user) {
		if(!function_exists('wp_generate_password'))
			frameSln::_()->loadPlugins();
		$newPass = wp_generate_password(mt_rand(12, 16));
		if(!empty($newPass)) {
			wp_set_password($newPass, $user['ID']);
			return $newPass;
		}
		return false;
	}
	public function sendNewPass($user, $newPass) {
		frameSln::_()->getModule('mail')->send(
			$user['user_email'], 
			__('Password changed', SLN_LANG_CODE),
			sprintf(__('Password on site %s for your admin account was changed due expiration date. Your new password is:<br /> %s', SLN_LANG_CODE), SLN_SITE_URL, $newPass));
	}
	public function checkChangePassMsg() {
		if(frameSln::_()->getModule('options')->get('admin_pass_change_enb')) {
			$passChangeRequire = get_user_meta(get_current_user_id(), SLN_CODE. '_pass_change_require');
			if($passChangeRequire) {
				$profileLink = get_edit_user_link();
				$html = '<div class="update-nag">'.
						sprintf(__('Your password has expired. Go to your profile <a href="%s">%s</a> and change it.', SLN_LANG_CODE), $profileLink, $profileLink)
						.'</div>';
				echo $html;
			}
		}
	}
	public function resetRemoveChangePassMsg($errors, $update, $user) {
		if(!$errors->get_error_codes()
			&& $update
			&& $user->ID == get_current_user_id()
			&& frameSln::_()->getModule('options')->get('admin_pass_change_enb')
			&& get_user_meta($user->ID, SLN_CODE. '_pass_change_require')
		) {
			$oldUserData = WP_User::get_data_by('id', $user->ID);
			if($oldUserData 
				&& !is_wp_error($oldUserData) 
				&& !wp_check_password($user->user_pass, $oldUserData->user_pass, $user->ID)	// Password should not be the same
			) {
				delete_user_meta($user->ID, SLN_CODE. '_pass_change_require');
			}
		}
	}
	public function checkLoginErrorDisable($errors) {
		if(frameSln::_()->getModule('options')->get('hide_login_errors_enb')) {
			$errors = null;
		}
		return $errors;
	}
	public function checkLoginPageRestrict() {
		
	}
	public function checkEmailAuth() {
		if(!$this->emailAuthEnabled())
			return;
		if(is_user_logged_in() && $this->emailCheckRoleEntry() && !$this->emailAuthAuthenticated() && !$this->isAllowedMethods()){
			$errors = array();
			if(!$this->getModel()->loginMailWasSent()) {
				if(!$this->getModel()->sendLoginMail()) {
					$errors = $this->getModel()->getErrors();
				}
			}
			$this->getView()->showEmailLoginForm( $errors );
			exit();
		} else {
			return;
		}
	}
	// Some allowed methods - for this module functionality usage
	public function isAllowedMethods() {
		if(frameSln::_()->getExecMod() == 'secure_login') {
			$allowedMethods = $this->getController()->getAuthPermissions();
			$execAction = frameSln::_()->getExecAction();
			if(in_array($execAction, $allowedMethods[ SLN_USERLEVELS ][ SLN_ADMIN ])
				/*&& !in_array($execAction, array('sendTest'))*/
			) {
				return true;
			}
		}
		return false;
	}
	public function emailAuthEnabled() {
		if(frameSln::_()->getModule('options')->get('email_auth_enb')) {
				return true;
		}
		return false;
	}
	public function emailCheckRoleEntry() {
		$optForRoles = frameSln::_()->getModule('options')->get('email_auth_opt_for_roles');
		if($optForRoles == 'specify') {
			global $current_user;
			$role = $current_user->roles[0];
			$tmp = frameSln::_()->getModule('options')->get('email_auth_roles');
			$roles = array();
			for($i=0;$i<count($tmp);$i++) {
				$roles[] = strtolower($tmp[$i]);
			}
			return in_array($role, $roles);
		} else {
			return true;
		}
	}
	public function emailAuthAuthenticated() {
		if(isset($_COOKIE[$this->_cookieName])){
			$data = utilsSln::decrypt($_COOKIE[$this->_cookieName], md5($this->_cookieSecureName));
			$tm = $data['tm'];
			$cryptTime = frameSln::_()->getModule('options')->get('email_auth_crypt_time');
			if($tm == $cryptTime) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public function emailSetAuthAuthenticated() {
		frameSln::_()->getModule('options')->getModel()->save('email_auth_crypt_time', time());
		$secureCookieHash = utilsSln::encrypt(
			array('tm' => frameSln::_()->getModule('options')->get('email_auth_crypt_time')),
			md5($this->_cookieSecureName)
		);
		if(setcookie($this->_cookieName, $secureCookieHash, time()+(24*3600))) {
			return true;
		} else {
			return false;
		}
	}
	public function clearCookie() {
		setcookie($this->_cookieName);
	}
}
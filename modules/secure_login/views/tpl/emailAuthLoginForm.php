<?php
if(!empty($this->errors)) {
	if(!function_exists('wp_shake_js')) {
		function wp_shake_js() {
			if ( wp_is_mobile() )
				return;
		?>
		<script type="text/javascript">
		addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
		function s(id,pos){g(id).left=pos+'px';}
		function g(id){return document.getElementById(id).style;}
		function shake(id,a,d){c=a.shift();s(id,c);if(a.length>0){setTimeout(function(){shake(id,a,d);},d);}else{try{g(id).position='static';wp_attempt_focus();}catch(e){}}}
		addLoadEvent(function(){ var p=new Array(15,30,15,0,-15,-30,-15,0);p=p.concat(p.concat(p));var i=document.forms[0].id;g(i).position='relative';shake(i,p,20);});
		</script>
		<?php
		}
	}
	add_action( 'login_head', 'wp_shake_js', 12 );
}
?>
<!DOCTYPE html> 
<!--[if IE 8]>
	<html xmlns="http://www.w3.org/1999/xhtml" class="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 8) ]><!-->
	<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<!--<![endif]-->
	<head>
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<title><?php _e('Email Authentication Login Page', SLN_LANG_CODE) ?></title>
		<?php
			wp_admin_css( 'login', true );
			do_action( 'login_enqueue_scripts' );
			do_action( 'login_head' );
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#slnEmailForm').submit(function(){
					jQuery(this).sendFormSln({
						msgElID: 'slnEmailMsg'
					,	onSuccess: function(res) {
							if(!res.error) {
								//toeReload();
								location.reload();
							}
						}
					});
					return false;
				});
				jQuery('#slnEmailLogBack').click(function(){
					jQuery.sendFormSln({
						msgElID: 'slnEmailMsg'
					,	data: {mod: 'secure_login', action: 'logout'}
					,	onSuccess: function(res) {
							if(!res.error) {
								toeReload();
							}
						}
					});
					return false;
				});
				jQuery('#slnEmailResendCode').click(function(){
					jQuery.sendFormSln({
						msgElID: 'slnEmailMsg'
					,	data: {mod: 'secure_login', action: 'resendCode'}
					});
					return false;
				});
			});
		</script>
		<style type="text/css">
			.wp-core-ui .button,.wp-core-ui .button-primary,.wp-core-ui .button-secondary{display:inline-block;text-decoration:none;font-size:13px;line-height:26px;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;border-width:1px;border-style:solid;-webkit-appearance:none;-webkit-border-radius:3px;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.wp-core-ui button::-moz-focus-inner,.wp-core-ui input[type=button]::-moz-focus-inner,.wp-core-ui input[type=reset]::-moz-focus-inner,.wp-core-ui input[type=submit]::-moz-focus-inner{border-width:0;border-style:none;padding:0}.wp-core-ui .button-group.button-large .button,.wp-core-ui .button.button-large{height:30px;line-height:28px;padding:0 12px 2px}.wp-core-ui .button-group.button-small .button,.wp-core-ui .button.button-small{height:24px;line-height:22px;padding:0 8px 1px;font-size:11px}.wp-core-ui .button-group.button-hero .button,.wp-core-ui .button.button-hero{font-size:14px;height:46px;line-height:44px;padding:0 36px}.wp-core-ui .button:active,.wp-core-ui .button:focus{outline:0}.wp-core-ui .button.hidden{display:none}.wp-core-ui input[type=reset],.wp-core-ui input[type=reset]:active,.wp-core-ui input[type=reset]:focus,.wp-core-ui input[type=reset]:hover{background:0 0;border:none;-webkit-box-shadow:none;box-shadow:none;padding:0 2px 1px;width:auto}.wp-core-ui .button,.wp-core-ui .button-secondary{color:#555;border-color:#ccc;background:#f7f7f7;-webkit-box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);vertical-align:top}.wp-core-ui p .button{vertical-align:baseline}.wp-core-ui .button-secondary:focus,.wp-core-ui .button-secondary:hover,.wp-core-ui .button.focus,.wp-core-ui .button.hover,.wp-core-ui .button:focus,.wp-core-ui .button:hover{background:#fafafa;border-color:#999;color:#222}.wp-core-ui .button-secondary:focus,.wp-core-ui .button.focus,.wp-core-ui .button:focus{-webkit-box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8)}.wp-core-ui .button-secondary:active,.wp-core-ui .button.active,.wp-core-ui .button.active:hover,.wp-core-ui .button:active{background:#eee;border-color:#999;color:#333;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}.wp-core-ui .button.active:focus{-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5),0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5),0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8)}.wp-core-ui .button-disabled,.wp-core-ui .button-secondary.disabled,.wp-core-ui .button-secondary:disabled,.wp-core-ui .button-secondary[disabled],.wp-core-ui .button.disabled,.wp-core-ui .button:disabled,.wp-core-ui .button[disabled]{color:#aaa!important;border-color:#ddd!important;background:#f7f7f7!important;-webkit-box-shadow:none!important;box-shadow:none!important;text-shadow:0 1px 0 #fff!important;cursor:default}.wp-core-ui .button-primary{background:#2ea2cc;border-color:#0074a2;-webkit-box-shadow:inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);box-shadow:inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);color:#fff;text-decoration:none}.wp-core-ui .button-primary.focus,.wp-core-ui .button-primary.hover,.wp-core-ui .button-primary:focus,.wp-core-ui .button-primary:hover{background:#1e8cbe;border-color:#0074a2;-webkit-box-shadow:inset 0 1px 0 rgba(120,200,230,.6);box-shadow:inset 0 1px 0 rgba(120,200,230,.6);color:#fff}.wp-core-ui .button-primary.focus,.wp-core-ui .button-primary:focus{border-color:#0e3950;-webkit-box-shadow:inset 0 1px 0 rgba(120,200,230,.6),0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);box-shadow:inset 0 1px 0 rgba(120,200,230,.6),0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8)}.wp-core-ui .button-primary.active,.wp-core-ui .button-primary.active:focus,.wp-core-ui .button-primary.active:hover,.wp-core-ui .button-primary:active{background:#1b7aa6;border-color:#005684;color:rgba(255,255,255,.95);-webkit-box-shadow:inset 0 1px 0 rgba(0,0,0,.1);box-shadow:inset 0 1px 0 rgba(0,0,0,.1);vertical-align:top}.wp-core-ui .button-primary-disabled,.wp-core-ui .button-primary.disabled,.wp-core-ui .button-primary:disabled,.wp-core-ui .button-primary[disabled]{color:#94cde7!important;background:#298cba!important;border-color:#1b607f!important;-webkit-box-shadow:none!important;box-shadow:none!important;text-shadow:0 -1px 0 rgba(0,0,0,.1)!important;cursor:default}.wp-core-ui .button-group{position:relative;display:inline-block;white-space:nowrap;font-size:0;vertical-align:middle}.wp-core-ui .button-group>.button{display:inline-block;-webkit-border-radius:0;border-radius:0;margin-right:-1px;z-index:10}.wp-core-ui .button-group>.button-primary{z-index:100}.wp-core-ui .button-group>.button:hover{z-index:20}.wp-core-ui .button-group>.button:first-child{-webkit-border-radius:3px 0 0 3px;border-radius:3px 0 0 3px}.wp-core-ui .button-group>.button:last-child{-webkit-border-radius:0 3px 3px 0;border-radius:0 3px 3px 0}.wp-core-ui .button-group>.button:focus{position:relative;z-index:1}@media screen and (max-width:782px){.wp-core-ui .button,.wp-core-ui .button.button-large,.wp-core-ui .button.button-small,a.preview,input#publish,input#save-post{padding:6px 14px;line-height:normal;font-size:14px;vertical-align:middle;height:auto;margin-bottom:4px}#media-upload.wp-core-ui .button{padding:0 10px 1px;height:24px;line-height:22px;font-size:13px}.media-frame.mode-grid .bulk-select .button{margin-bottom:0}.wp-core-ui .save-post-status.button{position:relative;margin:0 14px 0 10px}.press-this a.preview,.press-this input#publish,.press-this input#save-post,.press-this.wp-core-ui .button,.wp-core-ui.wp-customizer .button{padding:0 10px 1px;font-size:13px;line-height:26px;height:28px;margin:0;vertical-align:inherit}.interim-login .button.button-large{height:30px;line-height:28px;padding:0 12px 2px}}
		</style>
	</head>
	<body class="login wp-core-ui">
		<div id="login">
			<h1 class="centerHeader">
				<a href="http://wordpress.org/" id="WPLogo" title="Powered by WordPress"><?php echo get_bloginfo('name'); ?></a>
			</h1>
			<?php if(!empty($this->errors)) {?>
				<div id="login_error"><?php echo implode("<br />\n", $this->errors)?></div>
			<?php }?>
			<form id="slnEmailForm" action="" method="post">
				<p>
					<label for="auth_code">
						<?php printf(__('Mail with verification code was sent to your email %s. Check it and enter code from mail in text field below:', SLN_LANG_CODE), $this->usrEmail)?>
						<br />
						<input type="text" name="auth_code" id="auth_code" class="input" value="" size="20" />
					</label>
				</p>
				<p class="submit">
					<?php echo htmlSln::hidden('mod', array('value' => 'secure_login'))?>
					<?php echo htmlSln::hidden('action', array('value' => 'checkAuthCode'))?>
					<input type="button" id="slnEmailLogBack" name="back" class="button" value="<?php _e('Back', SLN_LANG_CODE); ?>" />
					<input type="button" id="slnEmailResendCode" name="resend_code" class="button" value="<?php _e('Re-send code', SLN_LANG_CODE); ?>" />
					<input type="submit" name="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Log In'); ?>" />
				</p>
				<p id="slnEmailMsg"></p>
			</form>
		</div>
		<?php do_action( 'login_footer' );?>
	</body>
</html>
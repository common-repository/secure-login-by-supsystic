jQuery(document).ready(function(){
	jQuery('#slnSecureLoginSaveBtn').click(function(){
		jQuery('#slnSecureLoginForm').submit();
		return false;
	});
	jQuery('#slnSecureLoginForm').submit(function(){
		jQuery(this).sendFormSln({
			btn: jQuery('#slnSecureLoginSaveBtn')
		});
		return false;
	});
	var adminIpDialog = jQuery('#slnAdminIpLoginDialog').dialog({
		autoOpen: false
	,	height: 490
	,	width: 480
	,	modal: true
	,	buttons: {
			Save: {
				text: toeLangSln('Save')
			,	click: function(event) {
					jQuery.sendFormSln({
						btn: event.target
					,	data: {mod: 'secure_login', action: 'saveAdminLoginIpsList', admin_ip_login_list: jQuery('#slnAdminLoginIpListTxt').val()}
					,	onSuccess: function(res) {
							slnCheckAdminLoginIpError();
						}
					});
				}
			}
		,	Cancel: {
				text: toeLangSln('Cancel')
			,	click: function() {
					adminIpDialog.dialog('close');
				}
			}
		}
	});
	// Add font awesome icon to save btn - not only pretty, but it also will show us loading indicator on save action
	var saveBtn = adminIpDialog.parents('.ui-dialog:first').find('.ui-dialog-buttonpane .ui-button:contains("'+ toeLangSln('Save')+ '")');
	saveBtn.prepend('<i class="fa fa-fw fa-save" style="padding: 0.6em 0"></i>')
	.find('.ui-button-text').css({
		'float': 'right'
	});
	
	jQuery('#slnAdminIpLoginShowListBtn').click(function(){
		adminIpDialog.dialog('open');
		return false;
	});
	slnCheckAdminLoginIpError();
	jQuery('#opt_valuesadmin_ip_login_enb_check').change(function(){
		slnCheckAdminLoginIpError();
	});
    jQuery('select[name="opt_values[email_auth_opt_for_roles]"]').change(function(){
        var roleType = jQuery(this).val();
        if(roleType == 'specify') {
            jQuery('#scrLoginEmailAuthData').show();
        } else {
            jQuery('#scrLoginEmailAuthData').hide();
        }
    });
    jQuery('select[name="opt_values[captcha_type]"]').change(function(){
        var captchaType = jQuery(this).val();
        if(captchaType == 'recaptcha') {
            jQuery('#scrLoginRecaptchaData').show();
        } else {
            jQuery('#scrLoginRecaptchaData').hide();
        }
    });
});
function slnCheckAdminLoginIpError() {
	jQuery('#slnAdminIpLoginCurrentError, #slnAdminIpLoginEmptyError').hide();
	if(jQuery('#opt_valuesadmin_ip_login_enb_check').attr('checked')) {
		var ipsList = jQuery.trim( jQuery('#slnAdminLoginIpListTxt').val() );
		if(!ipsList || ipsList == '') {
			jQuery('#slnAdminIpLoginEmptyError').show();
		} else {
			var currentIp = jQuery.trim(jQuery('#slnCurrentIp').html())
			,	currentIpFoundInList = false
			,	ipsList = ipsList.split("\n");
			for(var i in ipsList) {
				if(jQuery.trim(ipsList[ i ]) == currentIp) {
					currentIpFoundInList = true;
					break;
				}
			}
			if(!currentIpFoundInList) {
				jQuery('#slnAdminIpLoginCurrentError').show();
			}
		}
	}
}

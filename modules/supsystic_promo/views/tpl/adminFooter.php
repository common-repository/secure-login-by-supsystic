<div class="slnAdminFooterShell">
	<div class="slnAdminFooterCell">
		<?php echo SLN_WP_PLUGIN_NAME?>
		<?php _e('Version', SLN_LANG_CODE)?>:
		<a target="_blank" href="http://wordpress.org//plugins/supsystic-secure/changelog/"><?php echo SLN_VERSION?></a>
	</div>
	<div class="slnAdminFooterCell">|</div>
	<?php if(!frameSln::_()->getModule(implode('', array('l','ic','e','ns','e')))) {?>
	<div class="slnAdminFooterCell">
		<?php _e('Go', SLN_LANG_CODE)?>&nbsp;<a target="_blank" href="<?php echo $this->getModule()->preparePromoLink('http://supsystic.com/product/supsystic-secure/');?>"><?php _e('PRO', SLN_LANG_CODE)?></a>
	</div>
	<div class="slnAdminFooterCell">|</div>
	<?php }?>
	<div class="slnAdminFooterCell">
		<a target="_blank" href="http://wordpress.org//support/plugin/supsystic-secure"><?php _e('Support', SLN_LANG_CODE)?></a>
	</div>
	<div class="slnAdminFooterCell">|</div>
	<div class="slnAdminFooterCell">
		Add your <a target="_blank" href="http://wordpress.org//support/view/plugin-reviews/supsystic-secure?filter=5#postform">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on wordpress.org.
	</div>
</div>
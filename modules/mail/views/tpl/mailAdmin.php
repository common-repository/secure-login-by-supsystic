<form id="slnMailTestForm">
	<label>
		<?php _e('Send test email to')?>
		<?php echo htmlSln::text('test_email', array('value' => $this->testEmail))?>
	</label>
	<?php echo htmlSln::hidden('mod', array('value' => 'mail'))?>
	<?php echo htmlSln::hidden('action', array('value' => 'testEmail'))?>
	<button class="button button-primary">
		<i class="fa fa-paper-plane"></i>
		<?php _e('Send test', SLN_LANG_CODE)?>
	</button><br />
	<i><?php _e('This option allow you to check your server mail functionality', SLN_LANG_CODE)?></i>
</form>
<div id="slnMailTestResShell" style="display: none;">
	<?php _e('Did you received test email?', SLN_LANG_CODE)?><br />
	<button class="slnMailTestResBtn button button-primary" data-res="1">
		<i class="fa fa-check-square-o"></i>
		<?php _e('Yes! It work!', SLN_LANG_CODE)?>
	</button>
	<button class="slnMailTestResBtn button button-primary" data-res="0">
		<i class="fa fa-exclamation-triangle"></i>
		<?php _e('No, I need to contact my hosting provider with mail function issue.', SLN_LANG_CODE)?>
	</button>
</div>
<div id="slnMailTestResSuccess" style="display: none;">
	<?php _e('Great! Mail function was tested and working fine.', SLN_LANG_CODE)?>
</div>
<div id="slnMailTestResFail" style="display: none;">
	<?php _e('Bad, please contact your hosting provider and ask them to setup mail functionality on your server.', SLN_LANG_CODE)?>
</div>
<div style="clear: both;"></div>
<form id="slnMailSettingsForm">
	<table class="form-table" style="max-width: 450px;">
		<?php foreach($this->options as $optKey => $opt) { ?>
			<?php
				$htmlType = isset($opt['html']) ? $opt['html'] : false;
				if(empty($htmlType)) continue;
			?>
			<tr>
				<th scope="row" class="col-w-30perc">
					<?php echo $opt['label']?>
					<?php if(!empty($opt['changed_on'])) {?>
						<br />
						<span class="description">
							<?php 
							$opt['value'] 
								? printf(__('Turned On %s', SLN_LANG_CODE), dateSln::_($opt['changed_on']))
								: printf(__('Turned Off %s', SLN_LANG_CODE), dateSln::_($opt['changed_on']))
							?>
						</span>
					<?php }?>
				</th>
				<td class="col-w-10perc">
					<i class="fa fa-question supsystic-tooltip" title="<?php echo $opt['desc']?>"></i>
				</td>
				<td class="col-w-1perc">
					<?php echo htmlSln::$htmlType('opt_values['. $optKey. ']', array('value' => $opt['value'], 'attrs' => 'data-optkey="'. $optKey. '"'))?>
				</td>
				<td class="col-w-50perc">
					<div id="slnFormOptDetails_<?php echo $optKey?>" class="slnOptDetailsShell"></div>
				</td>
			</tr>
		<?php }?>
	</table>
	<?php echo htmlSln::hidden('mod', array('value' => 'mail'))?>
	<?php echo htmlSln::hidden('action', array('value' => 'saveOptions'))?>
	<button class="button button-primary">
		<i class="fa fa-fw fa-save"></i>
		<?php _e('Save', SLN_LANG_CODE)?>
	</button>
</form>



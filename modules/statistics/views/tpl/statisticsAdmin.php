<section>
	<div id="containerWrapper">
		<h3 class="nav-tab-wrapper" style="margin-bottom: 0px;">
		<?php foreach($this->statsTabs as $tabCode => $tab) { ?>
			<a class="nav-tab <?php echo ($tabCode == $this->currentStatsTab ? 'nav-tab-active' : '')?>" href="<?php echo $tab['url']?>">
				<?php echo $tab['label']?>
			</a>
		<?php }?>
			<?php if($this->haveData) {?>
			<button id="slnClearStats" class="button" data-tab="<?php echo $this->currentStatsTab?>">
				<i class="fa fa-trash-o"></i>
				<?php _e('Clear', SLN_LANG_CODE)?>
			</button>
			<?php }?>
		</h3>
		
		<div class="supsystic-item supsystic-panel">
			<?php if($this->currentStatsTab == 'detailed_login') { ?>
                <input type="text" id="slnDetailedLoginDateFrom" name="date-from" placeholder="<?php _e('Date from', SLN_LANG_CODE)?>" />
                <input type="text" id="slnDetailedLoginDateTo" name="date-to" placeholder="<?php _e('Date to', SLN_LANG_CODE)?>" />
				<table id="slnDetailedLoginTbl"></table>
				<div id="slnDetailedLoginTblNav"></div>
				<div id="slnDetailedLoginTblEmptyMsg" style="display: none;">
					<h3><?php _e('No data found', SLN_LANG_CODE)?></h3>
				</div>
			<?php } else { ?>
				<i><?php _e('Statistic count visit to frontend of your site, not admin area, and do not count visits by site administrator.')?></i>
				<hr /><div style="clear: both;"></div>
				<?php if($this->haveData) {?>
					<div id="slnStatGraph"></div>
					<table width="100%">
						<?php if(isset($this->requests['most_visited_url']['total_requests']) && !empty($this->requests['most_visited_url']['total_requests'])) {?>
						<tr>
							<td width="180px">
								<?php _e('Most visited URL', SLN_LANG_CODE)?>
								[<?php printf(__('visits %d', SLN_LANG_CODE), $this->requests['most_visited_url']['total_requests'])?>]:
							</td>
							<td><input type="text" readonly value="<?php echo esc_url($this->requests['most_visited_url']['url'])?>" style="width: 100%;" /></td>
						</tr>
						<?php }?>
						<?php if(isset($this->requests['most_active_ip']['total_requests']) && !empty($this->requests['most_active_ip']['total_requests'])) {?>
						<tr>
							<td width="180px">
								<?php _e('Most active IP', SLN_LANG_CODE)?>
								[<?php printf(__('visits %d', SLN_LANG_CODE), $this->requests['most_active_ip']['total_requests'])?>]:
							</td>
							<td><input type="text" readonly value="<?php echo $this->requests['most_active_ip']['ip']?>" /></td>
						</tr>
						<?php }?>
					</table>
				<?php } else { ?>
					<div class="alert alert-info"><?php _e('No data here for now', SLN_LANG_CODE)?></div>
				<?php }?>
			<?php }?>
		</div>
	</div>
</section>

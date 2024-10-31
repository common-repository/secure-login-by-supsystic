<section class="supsystic-bar">
	<div class="row">
		<div class="col-md-5">
			<div class="panel panel-default">
				<div class="panel-heading">Add to the permanent blacklist</div>
				<div class="panel-body">
					<button class="button button-primary slnPermBlacklistAddByIpBtn">
						<i class="fa fa-fw fa-plus"></i>
						<?php _e('by IP', SLN_LANG_CODE)?>
					</button>
					<button class="button button-primary slnPermBlacklistAddByCountryBtn">
						<i class="fa fa-fw fa-plus"></i>
						<?php _e('by Country', SLN_LANG_CODE)?>
					</button>
					<button class="button button-primary slnPermBlacklistAddByBrowserBtn">
						<i class="fa fa-fw fa-plus"></i>
						<?php _e('by Browser', SLN_LANG_CODE)?>
					</button>
				</div>
			</div>
		</div>
		<div class="col-md-5">
			<div class="panel panel-default">
				<div class="panel-heading">Add to the temporary blacklist</div>
				<div class="panel-body">
					<button class="button button-primary slnTempBlacklistAddByIpBtn">
						<i class="fa fa-fw fa-plus"></i>
						<?php _e('by IP', SLN_LANG_CODE)?>
					</button>
					<button class="button button-primary slnTempBlacklistAddByCountryBtn">
						<i class="fa fa-fw fa-plus"></i>
						<?php _e('by Country', SLN_LANG_CODE)?>
					</button>
					<button class="button button-primary slnTempBlacklistAddByBrowserBtn">
						<i class="fa fa-fw fa-plus"></i>
						<?php _e('by Browser', SLN_LANG_CODE)?>
					</button>
				</div>
			</div>
		</div>
	</div>
    <ul class="supsystic-bar-controls">
		<div style="clear: both;"></div>
		<li title="<?php _e('Your current IP address', SLN_LANG_CODE)?>">
			<i style="display: block; margin-top: 5px;">
				<?php _e('Your current IP address is', SLN_LANG_CODE)?>:
				<span id="slnCurrentIp"><?php echo $this->currentIp?></span>
			</i>
        </li>
		<li class="separator">|</li>
		<li title="<?php _e('Country', SLN_LANG_CODE)?>">
			<i style="display: block; margin-top: 5px;">
				<?php _e('Your Country', SLN_LANG_CODE)?>:
				<span id="slnCurrentCountryCode"><?php echo (empty($this->currentCountry) ? __('not detected', SLN_LANG_CODE) : $this->currentCountry)?></span>

			</i>
        </li>
		<li class="separator">|</li>
		<li title="<?php _e('Browser', SLN_LANG_CODE)?>">
			<i style="display: block; margin-top: 5px;">
				<?php _e('Your Browser', SLN_LANG_CODE)?>:
				<span id="slnCurrentBrowserName"><?php echo $this->currentBrowser['name']?></span>

			</i>
        </li>
    </ul>
	<div style="clear: both;"></div>
	<ul class="supsystic-bar-controls">
		<div style="clear: both;"></div>
		<li>
			<span id="slnBlockedCountriesMsg" style="display: none;">
				<?php _e('Site is blocked for <span id="slnBlockedCountriesCount" class="slnErrorMsg">%d</span> countries. For more info - click <a href="" class="slnBlacklistAddByCountryBtn">Add by Country</a> button', SLN_LANG_CODE)?>
			</span>
		</li>
	</ul>
	<div style="clear: both;"></div>
	<ul class="supsystic-bar-controls">
		<div style="clear: both;"></div>
		<li>
			<span id="slnBlockedBrowsersMsg" style="display: none;">
				<?php _e('Site is blocked for <span id="slnBlockedBrowsersCount" class="slnErrorMsg">%d</span> browsers. For more info - click <a href="#" class="slnBlacklistAddByBrowserBtn">Add by Browser</a> button', SLN_LANG_CODE)?>
			</span>
		</li>
	</ul>
	<div style="clear: both;"></div>
</section>
<section>
	<div id="containerWrapper">
		<div class="supsystic-item supsystic-panel">
			<hr />
			<ul class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected', SLN_LANG_CODE)?>">
					<button class="button" id="slnBlacklistRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', SLN_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Clear All')?>">
					<button class="button" id="slnBlacklistClearBtn" disabled data-toolbar-button>
						<?php _e('Clear', SLN_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', SLN_LANG_CODE)?>">
					<input id="slnBlacklistTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', SLN_LANG_CODE)?>">
				</li>
			</ul>
			<div id="slnBlacklistTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<?php echo htmlSln::selectbox('search_types', array('options' => $this->typesForSelect, 'attrs' => 'id="slnBlacklistTypeSel" onchange="slnBlacklistTypeSelChange();" class="supsystic-no-customize"', 'value' => $this->typeSelected))?>
			<div style="clear: both;"></div>
			<hr />
			<table id="slnBlacklistTbl"></table>
			<div id="slnBlacklistTblNav"></div>
			<div id="slnBlacklistTblEmptyMsg" style="display: none;">
				<h3><?php _e('No data found', SLN_LANG_CODE)?></h3>
			</div>
		</div>
	</div>
</section>

<!-- Add to the permanent blacklist by IP dialog-->
<div id="slnPermBlacklistAddByIpDlg" title="<?php _e('Add IPs to the permanent blacklist', SLN_LANG_CODE)?>">
	<form id="slnPermBlacklistAddByIpForm">
		<label>
			<?php _e('Enter one or more IPs, each new IP - from new line', SLN_LANG_CODE)?>:
			<textarea name="ips" style="float: left; width: 100%; height: 230px;"></textarea>
		</label>
		<?php echo htmlSln::hidden('mod', array('value' => 'blacklist'))?>
		<?php echo htmlSln::hidden('action', array('value' => 'addByIpToGroup'))?>
		<input type="hidden" name="type" value="permanent">
	</form>
	<div id="slnPermBlacklistAddByIpMsg"></div>
</div>
<!-- Add to the permanent blacklist by Country dialog-->
<div id="slnPermBlacklistAddByCountryDlg" title="<?php _e('Add Country(es) to the permanent blacklist', SLN_LANG_CODE)?>">
	<form id="slnPermBlacklistAddByCountryForm">
		<?php _e('Select country(es) for the permanent blacklist')?>:<br />
		<?php echo htmlSln::selectlist('country_ids[]', array('attrs' => 'class="chosen"', 'options' => $this->countryList, 'value' => $this->permBlockedCounties))?>
		<?php echo htmlSln::hidden('mod', array('value' => 'blacklist'))?>
		<?php echo htmlSln::hidden('action', array('value' => 'addByCountryToGroup'))?>
		<input type="hidden" name="type" value="permanent">
	</form>
	<div id="slnPermBlacklistAddCountryMsg"></div>
</div>
<!-- Add to the permanent blacklist by Browser dialog-->
<div id="slnPermBlacklistAddByBrowserDlg" title="<?php _e('Add Browser(s) to the permanent blacklist', SLN_LANG_CODE)?>">
	<form id="slnPermBlacklistAddByBrowserForm">
		<table width="100%" class="slnSmallTbl">
			<?php
			$perLine = 3;
			$i = 0;
			?>
			<?php foreach($this->browsersList as $browserName) { ?>
				<?php if(!$i || $i % $perLine == 0) { ?>
					<tr>
				<?php }?>
				<td>
					<label>
						<?php
						$htmlParams = array('value' => $browserName);
						if(in_array($browserName, $this->permBlockedBrowsers)) {
							$htmlParams['checked'] = true;
						}
						?>
						<?php echo htmlSln::checkbox('browser_names[]', $htmlParams)?>
						<?php echo $browserName?>
					</label>
				</td>
				<?php if($i && $i % $perLine == $perLine - 1) { ?>
					</tr>
				<?php }?>
				<?php $i++; }?>
		</table>
		<?php echo htmlSln::hidden('mod', array('value' => 'blacklist'))?>
		<?php echo htmlSln::hidden('action', array('value' => 'addByBrowserToGroup'))?>
		<input type="hidden" name="type" value="permanent">
	</form>
	<div id="slnPermBlacklistAddBrowserMsg"></div>
</div>

<!-- Add to the temporary blacklist by IP dialog-->
<div id="slnTempBlacklistAddByIpDlg" title="<?php _e('Add IPs to the temporary blacklist', SLN_LANG_CODE)?>">
	<form id="slnTempBlacklistAddByIpForm">
		<label>
			<?php _e('Enter one or more IPs, each new IP - from new line', SLN_LANG_CODE)?>:
			<textarea name="ips" style="float: left; width: 100%; height: 230px;"></textarea>
		</label>
		<?php echo htmlSln::hidden('mod', array('value' => 'blacklist'))?>
		<?php echo htmlSln::hidden('action', array('value' => 'addByIpToGroup'))?>
		<input type="hidden" name="type" value="temporary">
	</form>
	<div id="slnTempBlacklistAddByIpMsg"></div>
</div>
<!-- Add to the temporary blacklist by Country dialog-->
<div id="slnTempBlacklistAddByCountryDlg" title="<?php _e('Add Country(es) to the temporary blacklist', SLN_LANG_CODE)?>">
	<form id="slnTempBlacklistAddByCountryForm">
		<?php _e('Select country(es) for the temporary blacklist')?>:<br />
		<?php echo htmlSln::selectlist('country_ids[]', array('attrs' => 'class="chosen"', 'options' => $this->countryList, 'value' => $this->tempBlockedCounties))?>
		<?php echo htmlSln::hidden('mod', array('value' => 'blacklist'))?>
		<?php echo htmlSln::hidden('action', array('value' => 'addByCountryToGroup'))?>
		<input type="hidden" name="type" value="temporary">
	</form>
	<div id="slnTempBlacklistAddCountryMsg"></div>
</div>
<!-- Add to the temporary blacklist by Browser dialog-->
<div id="slnTempBlacklistAddByBrowserDlg" title="<?php _e('Add Browser(s) to the temporary blacklist', SLN_LANG_CODE)?>">
	<form id="slnTempBlacklistAddByBrowserForm">
		<table width="100%" class="slnSmallTbl">
			<?php
			$perLine = 3;
			$i = 0;
			?>
			<?php foreach($this->browsersList as $browserName) { ?>
				<?php if(!$i || $i % $perLine == 0) { ?>
					<tr>
				<?php }?>
				<td>
					<label>
						<?php
						$htmlParams = array('value' => $browserName);
						if(in_array($browserName, $this->tempBlockedBrowsers)) {
							$htmlParams['checked'] = true;
						}
						?>
						<?php echo htmlSln::checkbox('browser_names[]', $htmlParams)?>
						<?php echo $browserName?>
					</label>
				</td>
				<?php if($i && $i % $perLine == $perLine - 1) { ?>
					</tr>
				<?php }?>
				<?php $i++; }?>
		</table>
		<?php echo htmlSln::hidden('mod', array('value' => 'blacklist'))?>
		<?php echo htmlSln::hidden('action', array('value' => 'addByBrowserToGroup'))?>
		<input type="hidden" name="type" value="temporary">
	</form>
	<div id="slnTempBlacklistAddBrowserMsg"></div>
</div>
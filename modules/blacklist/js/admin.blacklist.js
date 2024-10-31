jQuery(document).ready(function(){
	var typeSelectHtml = jQuery('#slnBlacklistTypeSel').get(0).outerHTML;
	jQuery('#slnBlacklistTypeSel').remove();
	jQuery('#slnBlacklistTbl').jqGrid({
		url: slnBlacklistDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangSln('ID'), toeLangSln('IP'), toeLangSln('Date'), typeSelectHtml, toeLangSln('Action')]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '30', align: 'center'}
		,	{name: 'ip', index: 'ip', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'date_created', index: 'date_created', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'is_temp_label', index: 'is_temp_label', searchoptions: {sopt: ['eq']}, align: 'center', sortable: false}
		,	{name: 'action', index: 'action', sortable: false, search: false, align: 'center'}
		]
	,	postData: {
			search: {
				text_like: jQuery('#slnBlacklistTblSearchTxt').val()
			}
		}
	,	rowNum:10
	,	rowList:[10, 20, 30, 1000]
	,	pager: '#slnBlacklistTblNav'
	,	sortname: 'id'
	,	viewrecords: true
	,	sortorder: 'desc'
	,	jsonReader: { repeatitems : false, id: '0' }
	,	caption: toeLangSln('Current Blacklist')
	,	height: '100%' 
	,	emptyrecords: toeLangSln('You have no data in blacklist for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e) {
			var selectedRowIds = jQuery('#slnBlacklistTbl').jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#slnBlacklistTbl').getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#slnBlacklistRemoveGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_slnBlacklistTbl').prop('indeterminate', false);
					jQuery('#cb_slnBlacklistTbl').attr('checked', 'checked');
				} else {
					jQuery('#cb_slnBlacklistTbl').prop('indeterminate', true);
				}
			} else {
				jQuery('#slnBlacklistRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_slnBlacklistTbl').prop('indeterminate', false);
				jQuery('#cb_slnBlacklistTbl').removeAttr('checked');
			}
			slnCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			slnCheckUpdate('#cb_'+ jQuery(this).attr('id'));
		}
	,	gridComplete: function(a, b, c) {
			jQuery('#slnBlacklistRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_slnBlacklistTbl').prop('indeterminate', false);
			jQuery('#cb_slnBlacklistTbl').removeAttr('checked');
			if(jQuery('#slnBlacklistTbl').jqGrid('getGridParam', 'records'))	// If we have at least one row - allow to clear whole list
				jQuery('#slnBlacklistClearBtn').removeAttr('disabled');
			else
				jQuery('#slnBlacklistClearBtn').attr('disabled', 'disabled');
			// Custom checkbox manipulation
			slnInitCustomCheckRadio('#'+ jQuery(this).attr('id') );
			slnCheckUpdate('#cb_'+ jQuery(this).attr('id'));
		}
	,	loadComplete: function() {
			if (this.p.reccount === 0) {
				jQuery(this).hide();
				jQuery('#slnBlacklistTblEmptyMsg').show();
			} else {
				jQuery(this).show();
				jQuery('#slnBlacklistTblEmptyMsg').hide();
			}
		}
	});
	jQuery('#slnBlacklistTblNavShell').append( jQuery('#slnBlacklistTblNav') );
	jQuery('#slnBlacklistTblNav').find('.ui-pg-selbox').insertAfter( jQuery('#slnBlacklistTblNav').find('.ui-paging-info') );
	jQuery('#slnBlacklistTblNav').find('.ui-pg-table td:first').remove();
	jQuery('#slnBlacklistTblSearchTxt').keyup(function(){
		var searchVal = jQuery.trim( jQuery(this).val() );
		if(searchVal && searchVal != '') {
			slnGridDoListSearch({
				text_like: searchVal
			}, 'slnBlacklistTbl');
		}
	});
	slnInitCustomCheckRadio('#slnBlacklistTbl_cb');
	// Custom selects manipulation
	//slnInitCustomSelect('#slnBlacklistTblNav, #slnBlacklistTbl_type_label', true, true);
	
	
	jQuery('#slnBlacklistTblEmptyMsg').insertAfter(jQuery('#slnBlacklistTbl').parent());
	jQuery('#slnBlacklistTbl').jqGrid('navGrid', '#slnBlacklistTblNav', {edit: false, add: false, del: false});
	jQuery('#cb_slnBlacklistTbl').change(function(){
		jQuery(this).attr('checked') 
			? jQuery('#slnBlacklistRemoveGroupBtn').removeAttr('disabled')
			: jQuery('#slnBlacklistRemoveGroupBtn').attr('disabled', 'disabled');
	});
	jQuery('#slnBlacklistRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#slnBlacklistTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#slnBlacklistTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		jQuery.sendFormSln({
			btn: this
		,	data: {mod: 'blacklist', action: 'removeGroup', listIds: listIds}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#slnBlacklistTbl').trigger( 'reloadGrid' );
				}
			}
		});
		return false;
	});

	//slnBlacklistInitAddByBrowserDialog();

    slnPermBlacklistInitAddByIpDialog();
    slnPermBlacklistInitAddByCountryDialog();
    slnPermBlacklistInitAddByBrowserDialog();

    slnTempBlacklistInitAddByIpDialog();
    slnTempBlacklistInitAddByCountryDialog();
    slnTempBlacklistInitAddByBrowserDialog();

	jQuery('#slnBlacklistClearBtn').click(function(){
		if(confirm(toeLangSln('Clear whole blacklist?'))) {
			jQuery.sendFormSln({
				btn: this
			,	data: {mod: 'blacklist', action: 'clear'}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#slnBlacklistTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	// Blocked countries msg check
	slnCheckBlockedCountriesCnt();
	// Blocked browsers msg check
	slnCheckBlockedBrowserCnt();
	jQuery('.chosen').chosen();
});
function slnBlacklistInitAddByBrowserDialog() {
	var $container = jQuery('#slnBlacklistAddByBrowserDlg').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 480
	,	height: 220
	,	buttons:  {
			OK: function() {
				jQuery('#slnBlacklistAddByBrowserForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('.slnBlacklistAddByBrowserBtn').click(function(){
		jQuery('#slnBlacklistAddBrowserMsg').html('');
		$container.dialog('open');
		return false;
	});
	jQuery('#slnBlacklistAddByBrowserForm').submit(function(){
		var canContinue = true;
		/*if(strpos(ips, currentIp) !== false) {
			canContinue = confirm(toeLangSln('You entered your current IP - to blacklist.'+
				' This mean that your current computer will be blocked right after you will save this form.'+
				' Are you sure want to continue?'));
		}*/
		if(canContinue) {
			jQuery(this).sendFormSln({
				msgElID: 'slnBlacklistAddBrowserMsg'
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#slnBlacklistTbl').trigger( 'reloadGrid' );
						slnCheckBlockedBrowserCnt();
					}
				}
			});
		}
		return false;
	});
}
function slnBlacklistRemoveRow(id, link) {
	var msgEl = jQuery('<span />').insertAfter( link );
	jQuery.sendFormSln({
		msgElID: msgEl
	,	data: {mod: 'blacklist', action: 'remove', id: id}
	,	onSuccess: function(res) {
			if(!res.error) {
				jQuery('#slnBlacklistTbl').trigger( 'reloadGrid' );
			}
		}
	});
}
function slnCheckBlockedCountriesCnt() {
    var permBlockedCountriesCnt = jQuery('#slnPermBlacklistAddByCountryForm select[name="country_ids[]"] option:selected').size();
    var tempBlockedCountriesCnt = jQuery('#slnTempBlacklistAddByCountryForm select[name="country_ids[]"] option:selected').size();
    var blockedCountriesCnt = permBlockedCountriesCnt + tempBlockedCountriesCnt;
    if(blockedCountriesCnt) {
        jQuery('#slnBlockedCountriesMsg').show().find('#slnBlockedCountriesCount').html( blockedCountriesCnt );
    } else {
        jQuery('#slnBlockedCountriesMsg').hide();
    }
}
function slnCheckBlockedBrowserCnt() {
    var permBlockedBrowsersCnt = jQuery('#slnPermBlacklistAddByBrowserForm input[name="browser_names[]"]:checked').size();
    var tempBlockedBrowsersCnt = jQuery('#slnTempBlacklistAddByBrowserForm input[name="browser_names[]"]:checked').size();
    var blockedBrowsersCnt = permBlockedBrowsersCnt + tempBlockedBrowsersCnt;
    if(blockedBrowsersCnt) {
        jQuery('#slnBlockedBrowsersMsg').show().find('#slnBlockedBrowsersCount').html( blockedBrowsersCnt );
    } else {
        jQuery('#slnBlockedBrowsersMsg').hide();
    }
}
function slnBlacklistTypeSelChange() {
	slnGridDoListSearch({
		is_temp: jQuery('#slnBlacklistTypeSel').val()
	}, 'slnBlacklistTbl');
}
function slnPermBlacklistInitAddByIpDialog() {
    var $container = jQuery('#slnPermBlacklistAddByIpDlg').dialog({
        modal:    true
        ,	autoOpen: false
        ,	width: 460
        ,	height: 400
        ,	buttons:  {
            OK: function() {
                jQuery('#slnPermBlacklistAddByIpForm').submit();
            }
            ,	Cancel: function() {
                $container.dialog('close');
            }
        }
    });
    jQuery('.slnPermBlacklistAddByIpBtn').click(function(){
        jQuery('#slnPermBlacklistAddByIpMsg').html('');
        $container.dialog('open');
        return false;
    });
    jQuery('#slnPermBlacklistAddByIpForm').submit(function(){
        var ips = jQuery(this).find('[name=ips]').val()
            ,	currentIp = jQuery('#slnCurrentIp').html()
            ,	canContinue = true;
        if(strpos(ips, currentIp) !== false) {
            canContinue = confirm(toeLangSln('You entered your current IP - to the permanent blacklist.'+
            ' This mean that your current computer will be blocked right after you will save this form.'+
            ' Are you sure want to continue?'));
        }
        if(canContinue) {
            jQuery(this).sendFormSln({
                msgElID: 'slnPermBlacklistAddByIpMsg'
                ,	onSuccess: function(res) {
                    if(!res.error) {
                        jQuery('#slnBlacklistTbl').trigger( 'reloadGrid' );
                        jQuery('#slnPermBlacklistAddByIpForm').find('textarea').val('');
                    }
                }
            });
        }
        return false;
    });
}
function slnPermBlacklistInitAddByCountryDialog() {
    var $container = jQuery('#slnPermBlacklistAddByCountryDlg').dialog({
        modal:    true
        ,	autoOpen: false
        ,	width: 640
        ,	height: 410
        ,	buttons:  {
            OK: function() {
                jQuery('#slnPermBlacklistAddByCountryForm').submit();
            }
            ,	Cancel: function() {
                $container.dialog('close');
            }
        }
        ,	open: function() {
            jQuery('#slnPermBlacklistAddByCountryForm').find('.chosen-container').width('100%');
        }
    });
    jQuery('.slnPermBlacklistAddByCountryBtn').click(function(){
        jQuery('#slnPermBlacklistAddCountryMsg').html('');
        $container.dialog('open');
        return false;
    });
    jQuery('#slnPermBlacklistAddByCountryForm').submit(function(){
        jQuery(this).sendFormSln({
            msgElID: 'slnPermBlacklistAddCountryMsg'
            ,	onSuccess: function(res) {
                if(!res.error) {
                    jQuery('#slnBlacklistTbl').trigger( 'reloadGrid' );
                    slnCheckBlockedCountriesCnt();
                }
            }
        });
        return false;
    });
}
function slnPermBlacklistInitAddByBrowserDialog() {
    var $container = jQuery('#slnPermBlacklistAddByBrowserDlg').dialog({
        modal:    true
        ,	autoOpen: false
        ,	width: 480
        ,	height: 220
        ,	buttons:  {
            OK: function() {
                jQuery('#slnPermBlacklistAddByBrowserForm').submit();
            }
            ,	Cancel: function() {
                $container.dialog('close');
            }
        }
    });
    jQuery('.slnPermBlacklistAddByBrowserBtn').click(function(){
        jQuery('#slnPermBlacklistAddBrowserMsg').html('');
        $container.dialog('open');
        return false;
    });
    jQuery('#slnPermBlacklistAddByBrowserForm').submit(function(){
        jQuery(this).sendFormSln({
            msgElID: 'slnPermBlacklistAddBrowserMsg'
            ,	onSuccess: function(res) {
                if(!res.error) {
                    jQuery('#slnBlacklistTbl').trigger( 'reloadGrid' );
                    slnCheckBlockedBrowserCnt();
                }
            }
        });
        return false;
    });
}

function slnTempBlacklistInitAddByIpDialog() {
    var $container = jQuery('#slnTempBlacklistAddByIpDlg').dialog({
        modal:    true
        ,	autoOpen: false
        ,	width: 460
        ,	height: 400
        ,	buttons:  {
            OK: function() {
                jQuery('#slnTempBlacklistAddByIpForm').submit();
            }
            ,	Cancel: function() {
                $container.dialog('close');
            }
        }
    });
    jQuery('.slnTempBlacklistAddByIpBtn').click(function(){
        jQuery('#slnTempBlacklistAddByIpMsg').html('');
        $container.dialog('open');
        return false;
    });
    jQuery('#slnTempBlacklistAddByIpForm').submit(function(){
        var ips = jQuery(this).find('[name=ips]').val()
            ,	currentIp = jQuery('#slnCurrentIp').html()
            ,	canContinue = true;
        if(strpos(ips, currentIp) !== false) {
            canContinue = confirm(toeLangSln('You entered your current IP - to the temporary blacklist.'+
            ' This mean that your current computer will be blocked right after you will save this form.'+
            ' Are you sure want to continue?'));
        }
        if(canContinue) {
            jQuery(this).sendFormSln({
                msgElID: 'slnTempBlacklistAddByIpMsg'
                ,	onSuccess: function(res) {
                    if(!res.error) {
                        jQuery('#slnBlacklistTbl').trigger( 'reloadGrid' );
                        jQuery('#slnTempBlacklistAddByIpForm').find('textarea').val('');
                    }
                }
            });
        }
        return false;
    });
}
function slnTempBlacklistInitAddByCountryDialog() {
    var $container = jQuery('#slnTempBlacklistAddByCountryDlg').dialog({
        modal:    true
        ,	autoOpen: false
        ,	width: 640
        ,	height: 410
        ,	buttons:  {
            OK: function() {
                jQuery('#slnTempBlacklistAddByCountryForm').submit();
            }
            ,	Cancel: function() {
                $container.dialog('close');
            }
        }
        ,	open: function() {
            jQuery('#slnTempBlacklistAddByCountryForm').find('.chosen-container').width('100%');
        }
    });
    jQuery('.slnTempBlacklistAddByCountryBtn').click(function(){
        jQuery('#slnTempBlacklistAddCountryMsg').html('');
        $container.dialog('open');
        return false;
    });
    jQuery('#slnTempBlacklistAddByCountryForm').submit(function(){
        jQuery(this).sendFormSln({
            msgElID: 'slnTempBlacklistAddCountryMsg'
            ,	onSuccess: function(res) {
                if(!res.error) {
                    jQuery('#slnBlacklistTbl').trigger( 'reloadGrid' );
                    slnCheckBlockedCountriesCnt();
                }
            }
        });
        return false;
    });
}
function slnTempBlacklistInitAddByBrowserDialog() {
    var $container = jQuery('#slnTempBlacklistAddByBrowserDlg').dialog({
        modal:    true
        ,	autoOpen: false
        ,	width: 480
        ,	height: 220
        ,	buttons:  {
            OK: function() {
                jQuery('#slnTempBlacklistAddByBrowserForm').submit();
            }
            ,	Cancel: function() {
                $container.dialog('close');
            }
        }
    });
    jQuery('.slnTempBlacklistAddByBrowserBtn').click(function(){
        jQuery('#slnTempBlacklistAddBrowserMsg').html('');
        $container.dialog('open');
        return false;
    });
    jQuery('#slnTempBlacklistAddByBrowserForm').submit(function(){
        jQuery(this).sendFormSln({
            msgElID: 'slnTempBlacklistAddBrowserMsg'
            ,	onSuccess: function(res) {
                if(!res.error) {
                    jQuery('#slnBlacklistTbl').trigger( 'reloadGrid' );
                    slnCheckBlockedBrowserCnt();
                }
            }
        });
        return false;
    });
}
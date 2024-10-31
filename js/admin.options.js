var slnAdminFormChanged = [];
window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(slnAdminFormChanged.length)
		return 'Some changes were not-saved. Are you sure you want to leave?';
};
jQuery(document).ready(function(){
	if(typeof(slnActiveTab) != 'undefined' && slnActiveTab != 'main_page' && jQuery('#toplevel_page_securelogin-wp-supsystic').hasClass('wp-has-current-submenu')) {
		var subMenus = jQuery('#toplevel_page_securelogin-wp-supsystic').find('.wp-submenu li');
		subMenus.removeClass('current').each(function(){
			if(jQuery(this).find('a[href*="&tab='+ slnActiveTab+ '"]').size()) {
				jQuery(this).addClass('current');
			}
		});
	}
	
	// Timeout - is to count only user changes, because some changes can be done auto when form is loaded
	setTimeout(function() {
		// If some changes was made in those forms and they were not saved - show message for confirnation before page reload
		var formsPreventLeave = [];
		if(formsPreventLeave && formsPreventLeave.length) {
			jQuery('#'+ formsPreventLeave.join(', #')).find('input,select').change(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormSln(formId);
			});
			jQuery('#'+ formsPreventLeave.join(', #')).find('input[type=text],textarea').keyup(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormSln(formId);
			});
			jQuery('#'+ formsPreventLeave.join(', #')).submit(function(){
				adminFormSavedSln( jQuery(this).attr('id') );
			});
		}
	}, 1000);
	if(jQuery('.slnInputsWithDescrForm').size()) {
		jQuery('.slnInputsWithDescrForm').find('input[type=checkbox][data-optkey]').change(function(){
			var optKey = jQuery(this).data('optkey')
			,	descShell = jQuery('#slnFormOptDetails_'+ optKey);
			if(descShell.size()) {
				if(jQuery(this).attr('checked')) {
					descShell.slideDown( 300 );
				} else {
					descShell.slideUp( 300 );
				}
			}
		}).trigger('change');
	}
	// Tooltipster init
	jQuery('.supsystic-tooltip').tooltipster({
		contentAsHTML: true
	,	interactive: true
	,	speed: 250
	,	delay: 0
	,	animation: 'swing'
	,	position: 'top-left'
	,	maxWidth: 450
	});

	slnInitStickyItem();
	slnInitCustomCheckRadio();
	//slnInitCustomSelect();
	// If there are only one panel for whole page - let's make it's height equals to navigation sidebar height
	if(jQuery('.supsystic-item.supsystic-panel').size() == 1) {
		var fullPanelHeight = jQuery('.supsystic-navigation:first').height() - (jQuery('.supsystic-item.supsystic-panel').offset().top - jQuery('.supsystic-navigation:first').offset().top);
		jQuery('.supsystic-item.supsystic-panel').css({
			'min-height': fullPanelHeight
		}).attr('data-dev-hint', 'height is calculated in admin.options.js');
	}
});
function changeAdminFormSln(formId) {
	if(jQuery.inArray(formId, slnAdminFormChanged) == -1)
		slnAdminFormChanged.push(formId);
}
function adminFormSavedSln(formId) {
	if(slnAdminFormChanged.length) {
		for(var i in slnAdminFormChanged) {
			if(slnAdminFormChanged[i] == formId) {
				slnAdminFormChanged.pop(i);
			}
		}
	}
}
function checkAdminFormSaved() {
	if(slnAdminFormChanged.length) {
		if(!confirm(toeLangSln('Some changes were not-saved. Are you sure you want to leave?'))) {
			return false;
		}
		slnAdminFormChanged = [];	// Clear unsaved forms array - if user wanted to do this
	}
	return true;
}
function isAdminFormChanged(formId) {
	if(slnAdminFormChanged.length) {
		for(var i in slnAdminFormChanged) {
			if(slnAdminFormChanged[i] == formId) {
				return true;
			}
		}
	}
	return false;
}
/*Some items should be always on users screen*/
function slnInitStickyItem() {
	jQuery(window).scroll(function(){
		var stickiItemsSelectors = ['.ui-jqgrid-hdiv', '.supsystic-sticky']
		,	elementsUsePaddingNext = ['.ui-jqgrid-hdiv']	// For example - if we stick row - then all other should not offest to top after we will place element as fixed
		,	wpTollbarHeight = 32
		,	wndScrollTop = jQuery(window).scrollTop() + wpTollbarHeight
		,	footer = jQuery('.slnAdminFooterShell')
		,	footerHeight = footer && footer.size() ? footer.height() : 0
		,	docHeight = jQuery(document).height();
		for(var i = 0; i < stickiItemsSelectors.length; i++) {
			jQuery(stickiItemsSelectors[ i ]).each(function(){
				var element = jQuery(this);
				if(element && element.size()) {
					var scrollMinPos = element.offset().top
					,	prevScrollMinPos = parseInt(element.data('scrollMinPos'))
					,	useNextElementPadding = toeInArray(stickiItemsSelectors[ i ], elementsUsePaddingNext) !== -1;
					if(wndScrollTop > scrollMinPos && !element.hasClass('supsystic-sticky-active')) {
						element.addClass('supsystic-sticky-active').data('scrollMinPos', scrollMinPos).css({
							'top': wpTollbarHeight
						});
						if(useNextElementPadding) {
							element.addClass('supsystic-sticky-active-bordered');
							var nextElement = element.next();
							if(nextElement && nextElement.size()) {
								nextElement.data('prevPaddingTop', nextElement.css('padding-top'));
								nextElement.css({
									'padding-top': element.height()
								});
							}
						}
					} else if(!isNaN(prevScrollMinPos) && wndScrollTop <= prevScrollMinPos) {
						element.removeClass('supsystic-sticky-active').data('scrollMinPos', 0).css({
							'top': 0
						});
						if(useNextElementPadding) {
							element.removeClass('supsystic-sticky-active-bordered');
							var nextElement = element.next();
							if(nextElement && nextElement.size()) {
								var nextPrevPaddingTop = parseInt(nextElement.data('prevPaddingTop'));
								if(isNaN(nextPrevPaddingTop))
									nextPrevPaddingTop = 0;
								nextElement.css({
									'padding-top': nextPrevPaddingTop
								});
							}
						}
					} else {
						if(element.hasClass('supsystic-sticky-active') && footerHeight) {
							var elementHeight = element.height()
							,	heightCorrection = 32
							,	topDiff = docHeight - footerHeight - (wndScrollTop + elementHeight + heightCorrection);
							//console.log(topDiff);
							if(topDiff < 0) {
								//console.log(topDiff, elementTop + topDiff);
								element.css({
									'top': wpTollbarHeight + topDiff
								});
							} else {
								element.css({
									'top': wpTollbarHeight
								});
							}
						}
					}
				}
			});
		}
	});
}
function slnInitCustomCheckRadio(selector) {
	if(!selector)
		selector = document;
	jQuery(selector).find('input').iCheck('destroy').iCheck({
		checkboxClass: 'icheckbox_minimal'
	,	radioClass: 'iradio_minimal'
	}).on('ifChanged', function(e){
		// for checkboxHiddenVal type, see class htmlSln
		jQuery(this).trigger('change');
		if(jQuery(this).hasClass('cbox')) {
			var parentRow = jQuery(this).parents('.jqgrow:first');
			if(parentRow && parentRow.size()) {
				jQuery(this).parents('td:first').trigger('click');
			} else {
				var checkId = jQuery(this).attr('id');
				if(checkId && checkId != '' && strpos(checkId, 'cb_') === 0) {
					var parentTblId = str_replace(checkId, 'cb_', '');
					if(parentTblId && parentTblId != '' && jQuery('#'+ parentTblId).size()) {
						jQuery('#'+ parentTblId).find('input[type=checkbox]').iCheck('update');
					}
				}
			}
		}
	}).on('ifClicked', function(e){
		jQuery(this).trigger('click');
	});
}
function slnCheckUpdate(checkbox) {
	jQuery(checkbox).iCheck('update');
}
function slnCheckUpdateArea(selector) {
	jQuery(selector).find('input[type=checkbox]').iCheck('update');
}
/*function slnInitCustomSelect(selector, force, checkTblHeaders) {
	if(!selector)
		selector = document;
	var selectsForCustomize = jQuery(selector).find('select');
	if(!force) {
		selectsForCustomize = selectsForCustomize.not('.supsystic-no-customize')
	}
	if(checkTblHeaders) {
		selectsForCustomize.each(function(){
			jQuery(this).data('originalOffsetTop', jQuery(this).offset().top);
			jQuery(this).data('originalOffsetLeft', jQuery(this).offset().left);
		});
	}
	selectsForCustomize.chosen({
		disable_search_threshold: 10
	});
	if(checkTblHeaders) {
		selectsForCustomize.each(function(){
			jQuery(this).next('.chosen-container').css({
				'position': 'absolute'
			,	'top': jQuery(this).data('originalOffsetTop')
			,	'left': jQuery(this).data('originalOffsetLeft')
			});
		});
	}
}*/
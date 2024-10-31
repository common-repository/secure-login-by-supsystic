jQuery(document).ready(function(){
	if(slnCurrentStatsTab == 'detailed_login') {
		jQuery('#slnDetailedLoginTbl').jqGrid({
			url: slnDetailedLoginDataUrl
		,	datatype: 'json'
		,	autowidth: true
		,	shrinkToFit: true
		,	colNames:[toeLangSln('ID'), toeLangSln('User ID'), toeLangSln('Email'), toeLangSln('IP'), toeLangSln('Date'), toeLangSln('Actions')]
		,	colModel:[
				{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '30', align: 'center'}
			,	{name: 'uid', index: 'uid', searchoptions: {sopt: ['eq']}, width: '30', align: 'center'}
			,	{name: 'email', index: 'email', searchoptions: {sopt: ['eq']}, align: 'center', sortable: false}
			,	{name: 'ip', index: 'ip', searchoptions: {sopt: ['eq']}, align: 'center'}
			,	{name: 'date_created', index: 'date_created', searchoptions: {sopt: ['eq']}, align: 'center'}
            ,   {name: 'act', index: 'act', width: '200', sortable: false}
			]
		,   postData: {
                search: {
                    dateFrom: jQuery('#slnDetailedLoginDateFrom').val()
                ,   dateTo: jQuery('#slnDetailedLoginDateTo').val()
                }
            }
        ,   rowNum: 10
		,	rowList:[10, 20, 30, 1000]
		,	pager: '#slnDetailedLoginTblNav'
		,	sortname: 'id'
		,	viewrecords: true
		,	sortorder: 'desc'
		,	jsonReader: { repeatitems : false, id: '0' }
		,	caption: toeLangSln('Admins login')
		,	height: '100%' 
		,	emptyrecords: toeLangSln('You have no data about admin login or now.')
		,	loadComplete: function() {
				if (this.p.reccount === 0) {
					jQuery(this).hide();
                    jQuery('#slnClearStats').hide();
					jQuery('#slnDetailedLoginTblEmptyMsg').show();
				} else {
					jQuery(this).show();
                    jQuery('#slnClearStats').show();
					jQuery('#slnDetailedLoginTblEmptyMsg').hide();
				}
			}
		});
        jQuery('#slnDetailedLoginDateFrom').datepicker({
            dateFormat : 'yy-mm-dd'
        }).change(function(){
            slnGridDoListSearch({
                dateFrom: jQuery('#slnDetailedLoginDateFrom').val()
            ,   dateTo: jQuery('#slnDetailedLoginDateTo').val()
            }
            ,   'slnDetailedLoginTbl'
            )
        });
        jQuery('#slnDetailedLoginDateTo').datepicker({
            dateFormat : 'yy-mm-dd'
        }).change(function(){
            slnGridDoListSearch({
                dateFrom: jQuery('#slnDetailedLoginDateFrom').val()
            ,   dateTo: jQuery('#slnDetailedLoginDateTo').val()
            }
            ,   'slnDetailedLoginTbl'
            )
        });
	} else if(slnStatRequests && slnStatRequests.graph && slnStatRequests.graph.length && slnStatRequests.graph[0] && slnStatRequests.graph[0].points && slnStatRequests.graph[0].points.length) {
		var plotData = [];
		for(var i = 0; i < slnStatRequests.graph.length; i++) {
			plotData.push([]);
			for(var j = 0; j < slnStatRequests.graph[i]['points'].length; j++) {
				plotData[i].push([slnStatRequests.graph[ i ]['points'][ j ]['date'], parseInt(slnStatRequests.graph[ i ]['points'][ j ]['total_requests'])]);
			}
		}
		jQuery.jqplot('slnStatGraph', plotData, {
			axes: {
				xaxis: {
					label: toeLangSln('Date')
				,	labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer
				,	renderer:	jQuery.jqplot.DateAxisRenderer
				,	tickOptions:{formatString:'%b %#d, %Y'},
				}
			,	yaxis: {
					label: toeLangSln('Visits')
				,	labelRenderer: jQuery.jqplot.CanvasAxisLabelRenderer
				}
			}
		,	highlighter: {
				show: true
			,	sizeAdjust: 7.5
			,	tooltipContentEditor: function(str, seriesIndex, pointIndex, jqPlot) {
					if(slnStatRequests.graph[ seriesIndex ] && slnStatRequests.graph[ seriesIndex ].label) {
						return slnStatRequests.graph[ seriesIndex ].label+ ' '+ str;
					}
					return str;
				}
			}
		,	cursor: {
				show: true
			,	zoom: true
			}
		});
	}
	jQuery('#slnClearStats').click(function(){
		if(confirm('Are you sure want to clear '+ jQuery.trim(jQuery('#containerWrapper .nav-tab.nav-tab-active').html())+ ' statistics?')) {
			jQuery.sendFormSln({
				btn: this
			,	data: {mod: 'statistics', action: 'clear', tab: jQuery(this).data('tab')}
			,	onSuccess: function(res) {
					if(!res.error) {
						toeReload();
					}
				}
			});
		}
		return false;
	});
});
function addToBlacklist(ip, type, btn) {
    jQuery.sendFormSln({
        btn: btn
        ,	data: {mod: 'blacklist', action: 'addToBlacklist', ip: ip, is_temp: type}
        ,	onSuccess: function(res) {
            if(!res.error) {
                jQuery('#slnDetailedLoginTbl').trigger('reloadGrid');
            }
        }
    });
}
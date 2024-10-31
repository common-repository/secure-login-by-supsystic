jQuery(document).ready(function(){
	jQuery('#slnMailTestForm').submit(function(){
		jQuery(this).sendFormSln({
			btn: jQuery(this).find('button:first')
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#slnMailTestForm').slideUp( 300 );
					jQuery('#slnMailTestResShell').slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('.slnMailTestResBtn').click(function(){
		var result = parseInt(jQuery(this).data('res'));
		jQuery.sendFormSln({
			btn: this
		,	data: {mod: 'mail', action: 'saveMailTestRes', result: result}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#slnMailTestResShell').slideUp( 300 );
					jQuery('#'+ (result ? 'slnMailTestResSuccess' : 'slnMailTestResFail')).slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('#slnMailSettingsForm').submit(function(){
		jQuery(this).sendFormSln({
			btn: jQuery(this).find('button:first')
		});
		return false; 
	});
});
jQuery.fn.liveUpdate = function(list){
	list = jQuery(list);

	if ( list.length ) {
		var rows = list.children('li'),
			cache = rows.map(function(){
				return jQuery(this).text().toLowerCase();
			});
			
		this
			.keyup(filter).keyup()
			.parents('form').submit(function(){
				return false;
			});
	}
		
	return this;
		
	function filter(){
		var term = jQuery.trim( jQuery(this).val().toLowerCase() ), scores = [];
		
		if ( !term ) {
			rows.show().addClass('keynav withoutfocus');
		} else {
			rows.hide().removeClass('keynav withfocus withoutfocus');

			cache.each(function(i){
				if ( this.indexOf( term ) > -1 ) {
					jQuery(rows[i]).show().addClass('keynav withoutfocus');
				}
			});
		}
	}
};

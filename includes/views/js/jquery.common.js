
jQuery(document).ready(function() {	
	// 목록에서 아이템 삭제
	jQuery(".button-submitdelete").click(function(event) {
		var href = jQuery(this).attr("href");
		
		if ( confirm( "Are you sure delete?" ) ) {
			window.location.href = href;
		}

		return false;
	});

	// 목록에서 일괄작업
	jQuery(".button-submitaction").click(function(event) {
		var $eldiv = jQuery(this).closest("div");
		var action = $eldiv.find('select[name="action"]').val();
		var string = "";

		if ( action == "-1" )
			return false;

		if ( is_checked( "checkbox-list" ) == false ) {
			alert("1개이상 선택하세요!");
			return false;
		}

		jQuery('select[name="action"]').val( action );

		switch ( action ) {
			case "delete":
				string = "Are you sure delete?";
			break;
		}
		
		if ( confirm( string ) ) {
			return true;
		}

		return false;
	});

	// 체크박스 확인
	function is_checked( elements_name ) {
	    var checked = false;
		var chkcnt  = jQuery("." + elements_name + ":checkbox:checked").length;

    	if ( chkcnt > 0 ) {
            checked = true;
        }
	
	    return checked;
	}
});
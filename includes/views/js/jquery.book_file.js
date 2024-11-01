
(function($){

    // 가져오기
	$("#button-upload").click(function(event) {
		var $btn = $(this);
		var $elp = $(".upload-progress");
		var data = new FormData();

		if ( !$("#book_file").val() ) {
			alert("엑셀파일(csv)을 추가해 주세요!");
			return;
		}

		data.append( "action",    "sms4wp_ajax_book_file_upload" );
		if ( $("#book_file").val() )
			data.append( "book_file", $("#book_file")[0].files[0], $("#book_file")[0].files[0].name ); // 첨부파일1
		data.append( "gr_parent", $("select[name=gr_parent]").val() ); // group
		data.append( "nonce",     $("input[name='_wpnonce_upload']").val() ); 


		$btn.css("display", "none");
		$elp.css("background", "url(" + inc_view_img_url + "/ajax-loader.gif) no-repeat");

        $.ajax({
			url : ajaxurl,
			data : data,
			type : "post",
			cache : false,
			dataType : "json",
			processData : false,
			contentType : false,
			success: function(response) {
				$btn.css("display", "inline-block");
				$elp.css("background", "url()");

				switch ( response ) {
					case "success":
						alert("complete");
					break;

					case 801:
						alert("파일을 선택해주세요.");
					break;

					case 802:
						alert("올바른 파일이 아닙니다.");
					break;

					case 803:
						alert("csv파일만 허용합니다.");
					break;

					default:
						alert("failed" + response);
					break;
				}
				return;
				// window.location.reload(true);
			}
		});
	});


    // 내보내기
	$("#button-download").click(function(event) {
		var $btn = $(this);
		var $elp = $(".download-progress");
		var data = new FormData();

		if ( !confirm("엑셀출력 하시겠습니까?") ) {
			return;
		}

		data.append( "action",    "sms4wp_ajax_book_file_download" );
		data.append( "gr_parent", $("select[name=gr_parent2]").val() ); // group
		data.append( "nonce",     $("input[name='_wpnonce_download']").val() ); 

		$elp.css("background", "url(" + inc_view_img_url + "/ajax-loader.gif) no-repeat");
		$btn.css("display", "none");

        $.ajax({
			url : ajaxurl,
			data : data,
			type : "post",
			cache : false,
			dataType : "json",
			processData : false,
			contentType : false,
			success: function(response) {
				$btn.css("display", "inline-block");
				$elp.css("background", "url()");

				switch ( response ) {
					case "success": case "":
						// alert("complete");
						alert(response);
					break;

					case 801:
						alert("다운로드 할 휴대폰번호 그룹을 선택해주세요.");
					break;

					case 802:
						alert("출력 수신자가 없습니다.");
					break;

					default:
						alert("failed" + response);
					break;
				}
				return;
				// window.location.reload(true);
			}
		});
	});
})(jQuery);

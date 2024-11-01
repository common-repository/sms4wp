$(function() {
	$( "#reservation_date" ).datepicker();
	$( "#reservation_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );

	$('#reservation_time').timepicker({ 'timeFormat': 'H:i:s', 'step': 15 });
});

$(document).ready(function() {	
	var page_rows = 12;
    var paged = 1;
    var page_tmp;
    var s = '';

    var tpage_rows = 5;
    var tpaged = 1;
    var tpage_tmp;

    // 수신자 스크롤시 자동으로 다음페이지 출력
    $('.receiver-list').scroll(function(){
    	var scrollPosition = $(this).scrollTop() + $(this).outerHeight();
	    var divTotalHeight = $(this)[0].scrollHeight;

        if  ( scrollPosition >= divTotalHeight - 10 ){
            if( page_tmp != paged ) {
                receiver_list();
            }
        }
    }); 
    // 수신자 검색
	$(".button-receiver-search").click(function(event) {
		s     = $("#receiver-search").val();
		paged = 1;

		if ( s == "" ) {
			alert( "search word!" );
			return;
		}

		$('.add-sms4wp-receiver > li').each(function(index, el) {
			$(this).remove();
		});

		$('.receiver-list').css("background", "url(" + inc_view_img_url + "/default-loading.gif) no-repeat center");

        receiver_list();
	});
	// 수신자 검색 (enter)
	$("#receiver-search").keypress(function(event) {
		if ( event.which == 13 ) {
			s     = $(this).val();
			paged = 1;

			if ( s == "" ) {
				alert( "search word!" );
				return;
			}

			$('.add-sms4wp-receiver > li').each(function(index, el) {
				$(this).remove();
			});

			$('.receiver-list').css("background", "url(" + inc_view_img_url + "/default-loading.gif) no-repeat center");

	        receiver_list();
	    }
	});
	// 수신자 목록 
	function receiver_list() {
        var nonce = receivers_nonce;
        page_tmp  = paged;

        $('.receiver-list').css("background", "url(" + inc_view_img_url + "/default-loading.gif) no-repeat center");

        $.ajax({
            dataType : "html",
            type     : 'post',
            url      : ajaxurl,
            data     : {action: "sms4wp_ajax_receivers", page_rows: page_rows, paged: paged, s: s, nonce: nonce},
            success : function(d){
                // console.log(d);
                if ( d != 'false' ){
                    $('.receiver-list').css("background", "url()");
                    $('.add-sms4wp-receiver').append(d);
                    paged++;

                    setTimeout(function(){
                    	//--
                    },2000);
                }
                return;
            }
        });
    }

    // 메시지 보내기 버튼 클릭
	$(".submit").on("click", "#message-submit", function() {
        var $elp    = $(this).closest("p");
		var nonce   = message_nonce;
        var button  = $elp.html(); // 보내기 버튼 

        var receivers = new Array(); // 수신자 목록
        var groups    = new Array(); // 그룹 목록
        var grChk     = false;

        $(".receiver-group > dd").each(function(index, el) {
        	var re_id = $(this).attr("re_id");
        	var gr_id = $(this).attr("gr_id");
        	
        	if ( re_id != undefined ) {
        		receivers.push( re_id );
        		grChk = true;
        	}
        	else if ( gr_id != undefined ) {
        		groups.push( gr_id );
        		grChk = true;
        	}
        });
        // alert( receivers + " :: " + groups );
        // return;

        if ( $("#sender_phone").val() == "" ) {
        	alert( "보내는 사람 연락처를 입력하세요!" );
        	$("#sender_phone").focus();
        	return;
        }
        
        if ( $("#receiver_phone").val() == "" && grChk == false ) {
        	alert( "받는 사람 또는 수신자, 수신자그룹을 추가하세요!" );
        	$("#receiver_phone").focus();
        	return;
        }

        if ( $("#message_body").val() == "" ) {
        	alert( "메시지 내용을 입력하세요!" );
        	$("#message_body").focus();
        	return;
        }

        var data = new FormData();
        data.append( "action",     		 "sms4wp_ajax_msg_sends" );
        data.append( "nonce",     		 nonce ); 
        data.append( "message_type",     $(".message_type:checked").val() ); 			// SMS, LMS, MMS
        data.append( "sender_phone",     $("#sender_phone").val() ); 					// 보내는 사람
        data.append( "receiver_phone",   $("#receiver_phone").val().split(',') ); 		// 받는 사람
        data.append( "message_body",     $("#message_body").val() ); 					// 보내는 메시지
        data.append( "message_subject",  $("#message_subject").val() ); 				// mms 제목
        data.append( "reservation_date", $("#reservation_date_current").val() ); 				// 예약일
        data.append( "reservation_time", $("#reservation_time_current").val() ); 				// 예약시간
        if ( $("#add_file1").val() )
	        data.append( "add_file1", $("#add_file1")[0].files[0], $("#add_file1")[0].files[0].name ); 				// 첨부파일1
        // data.append( "add_file2",        $("#add_file2")[0].files[0] ); 				// 첨부파일2
        data.append( "receivers",        receivers ); 									// 수신자 목록
        data.append( "groups",        	 groups ); 										// 그룹목록

        // $.each($("#add_file1")[0].files, function(key, value){
        // 	alert( key+'::'+value);
        // });
        // return;
        console.log( $("#reservation_time_current").val() );

        $elp.html('');
        $elp.css("background", "url(" + inc_view_img_url + "/loadingAnimation.gif) no-repeat");

		$.ajax({
			url : ajaxurl,
			data : data,
			type : "post",
			cache : false,
			dataType : "json",
			processData : false,
			contentType : false,
			success: function(response) {
		        // setTimeout(function(){
		        // 	$elp.css("background", "url()");
		        // 	$elp.html(button);
		        // },2000);   

				$elp.css("background", "url()");
	        	$elp.html(button);

				if( response.success || response.failed ) {
					alert("성공:"+response.success+", 실패:"+response.failed);
				}
				else {
					alert("failed " + response.error);
				}
				// window.location.reload(true);
			},
			progress: function(e) {
				// alert( e.lengthComputable );
				// if ( e.lengthComputable ) {
				// 	var pct = (e.loaded / e.total) * 100;
				// 	$('#prog')
				// 	.progressbar('option', 'value', pct)
				// 	.children('.ui-progressbar-value')
				// 	.html(pct.toPrecision(3) + '%')
				// 	.css('display', 'block');
				// } 
				// else {
				// 	console.warn('Content Length not reported!');
				// }
			}
		});

	});

	//받는 사람 추가 창 열기
	$('.sms4wp-btn-receiver-group').click(function (e) {
		var display = $('.sms4wp-msg-inside').css("display");

		if ( display == "none" ) {
			$('.sms4wp-msg-inside').slideDown( function() {
				$( this ).css( 'height', 'auto' ); // so that the .accordion-section-content won't overflow
			} );
			$('.sms4wp-msg-inside').show();
		}
		else {
			$('.sms4wp-msg-inside').slideUp( function() {
				$('.sms4wp-msg-inside').hide();
			} );
		}
	});	
    // 메시지 그룹 추가
    $(".add-sms4wp-group").click(function(event) {
    	var gr_id = $(".sms4wp-groups option:selected").val();
    	var gr_name = $(".sms4wp-groups option:selected").text();
    	var $receivers = $(".receiver-group");
    	// alert( gr_id + gr_name );
    	if ( gr_id != "0" ) {
			var group = '<dd gr_id="' + gr_id + '"><span class="type">그 &nbsp;룹:</span> ' + gr_name + ' <span class="receiver-delete">X</span></dd>';
			$receivers.append(group);
		}
    });
    // 메시지 수신자 추가
    $(".add-sms4wp-receiver").on("click", ".add-receiver", function() {
    	var $receivers = $(".receiver-group");

    	var re_if = $(this).text();
    	var re_id = $(this).attr("re_id");

		var receiver = '<dd re_id="' + re_id + '"><span class="type">수신자:</span> ' + re_if + ' <span class="receiver-delete">X</span></dd>';
		$receivers.append(receiver);
    });
    // 수신자,그룹 삭제
    $(".receiver-group").on("click", ".receiver-delete", function(event) {
    	event.preventDefault();
    	$(this).parent().remove();
    });


    // 템플릿 추가 창 열기
	$('.sms4wp-btn-template').click(function (e) {
		var display = $('.sms4wp-template-inside').css("display");

		if ( display == "none" ) {
			$('.sms4wp-template-inside').slideDown( function() {
				$( this ).css( 'height', 'auto' ); // so that the .accordion-section-content won't overflow
			} );
			$('.sms4wp-template-inside').show();
		}
		else {
			$('.sms4wp-template-inside').slideUp( function() {
				$('.sms4wp-template-inside').hide();
			} );
		}

		return false;
	});	
    // 템플릿 스크롤시 자동으로 다음페이지 출력
    $('.template-list').scroll(function(){
    	var scrollPosition = $(this).scrollTop() + $(this).outerHeight();
	    var divTotalHeight = $(this)[0].scrollHeight;

        if  ( scrollPosition >= divTotalHeight - 10 ){
            if( tpage_tmp != tpaged ) {
                template_list();
            }
        }
    }); 
    // 템플릿 목록 보기
	function template_list() {
        var nonce = template_nonce;
        tpage_tmp = tpaged;

        $(".template-list").css("background", "url(" + inc_view_img_url + "/default-loading.gif) no-repeat bottom");
        $(".add-sms4wp-template").css("margin-bottom", "20px");

        $.ajax({
            dataType : "html",
            type     : 'post',
            url      : ajaxurl,
            data     : {action: "sms4wp_ajax_templates", page_rows: tpage_rows, paged: tpaged, s: s, nonce: nonce},
            success : function(d){
                if ( d != 'false' ){
                    $('.template-list').css("background", "url()");
                    $('.add-sms4wp-template').append(d);
                    if ( d )
	                    tpaged++;
                    
                    setTimeout(function(){
                    	//-- 
                    },2000);    
                }
                return;
            },
	        error : function(r) { 
	        	 console.log("Failed : " + r); 
	    	},
	    	complete : function() {
	    		$(".add-sms4wp-template").css("margin-bottom", "0px");
	    	}
        }); 
    }
    // 템플릿 클릭시 보내는 메시지로 보내기
    $(".add-sms4wp-template").on("click", ".add-template", function(event) {
    	var template = $(this).find(".message").text();
		$("#message_body").find('div').css("display", "none");
		$("#message_body").val(template);
    });
    // 메시지 내용 템플릿 저장하기
    $(".sms4wp-save-template").click(function(event) {
    	var message = $("#message_body").val();
    	var subject = '';
		var nonce   = $(":input[name='nonce']").val();

		if ( $("#message_subject").val() ) {
			subject = $("#message_subject").val();
		}
		
        $("#message_body").css("background", "#fff url(" + inc_view_img_url + "/default-loading.gif) no-repeat center");
        $("#message_body").css("color", "#FFFFFF");

        if ( message == "" ) {
        	$("#message_body").css("background", "#fff url()");
	        $("#message_body").css("color", "#333333");

        	alert("blank message");
        	return;
        }

        if ( !confirm("message save?") ) {
        	$("#message_body").css("background", "#fff url()");
	        $("#message_body").css("color", "#333333");
        	return;
        }

        $.ajax({
            dataType : "json",
            type     : 'post',
            url      : ajaxurl,
            data     : {action: "sms4wp_ajax_save_template", message: message, subject: subject, nonce: nonce},
            success : function(d){
                // console.log(d);

                $("#message_body").css("background", "#fff url()");
		        $("#message_body").css("color", "#333333");
		        
 				if ( d == '100' ) 
 					alert( "저장완료" );
 				else 
 					alert( d );
                
                setTimeout(function(){
                	//-- 
                },2000);    

                return;
            }
        }); 
    });
    template_list(); // 처음 템플릿 출력


	//select all the a tag with name equal to modal
	// $('a[name=modal]').click(function(e) {
	// 	//Cancel the link behavior
	// 	e.preventDefault();
		
	// 	//Get the A tag
	// 	var id = $(this).attr('href');
	
	// 	//Get the screen height and width
	// 	var maskHeight = $(document).height();
	// 	var maskWidth = $(window).width();
	
	// 	//Set heigth and width to mask to fill up the whole screen
	// 	$('#mask').css({'width':maskWidth,'height':maskHeight});
		
	// 	//transition effect		
	// 	$('#mask').fadeIn('fast');	
	// 	$('#mask').fadeTo("slow",0.8);	
	
	// 	//Get the window height and width
	// 	var winH = $(window).height();
	// 	var winW = $(window).width();
              
	// 	//Set the popup window to center
	// 	$(id).css('top',  winH/2-$(id).height()/2);
	// 	$(id).css('left', winW/2-$(id).width()/2);
	
	// 	//transition effect
	// 	$(id).fadeIn('fast'); 

	// 	$("#dialog-content").load("<?php echo plugins_url(); ?>/sms4wp/includes/sms4wp_options1_senders.php");
	
	// });
	
	
	// MMS 첨부파일 창 열기
	$('.message_type').click(function (e) {
		var msg_type = $(this).val();

		if ( msg_type == "MMS" ) {
			$('.msg_mms').slideDown( function() {
				$( this ).css( 'height', 'auto' ); // so that the .accordion-section-content won't overflow
			} );
			$('.msg_mms').show();

			$('.msg_type_mms').addClass('sms_selected');
			$('.msg_type_sms').removeClass('sms_selected');
			$('.msg_type_lms').removeClass('sms_selected');
		}
		else if ( msg_type == "LMS" ) {
			$('.msg_mms').slideUp( function() {
				$('.msg_mms').hide();
			} );

			$('.msg_type_mms').removeClass('sms_selected');
			$('.msg_type_sms').removeClass('sms_selected');
			$('.msg_type_lms').addClass('sms_selected');
		}
		else {
			$('.msg_mms').slideUp( function() {
				$('.msg_mms').hide();
			} );

			$('.msg_type_mms').removeClass('sms_selected');
			$('.msg_type_sms').addClass('sms_selected');
			$('.msg_type_lms').removeClass('sms_selected');
		}
	});	

	// //if close button is clicked
	// $('.window .close').click(function (e) {
	// 	//Cancel the link behavior
	// 	e.preventDefault();
		
	// 	$('#mask').hide();
	// 	$('.window').hide();
	// });		
	
	// //if mask is clicked
	// $('#mask').click(function () {
	// 	$(this).hide();
	// 	$('.window').hide();
	// });			

	// $(window).resize(function () {
	 
 // 		var box = $('#boxes .window');
 
 //        //Get the screen height and width
 //        var maskHeight = $(document).height();
 //        var maskWidth = $(window).width();
      
 //        //Set height and width to mask to fill up the whole screen
 //        $('#mask').css({'width':maskWidth,'height':maskHeight});
               
 //        //Get the window height and width
 //        var winH = $(window).height();
 //        var winW = $(window).width();

 //        //Set the popup window to center
 //        box.css('top',  winH/2 - box.height()/2);
 //        box.css('left', winW/2 - box.width()/2);
	 
	// });

	// 메시지 입력시 자동 SMS<->LMS 전환 
	$("#message_body").keyup(function() {
	    var msg_body = $(this).val();
	    var msg_type = $('.message_type').val();

	    var count = 0;
	    var word  = '';

	    for ( var i = 0; i < msg_body.length; i++ ) {
	        word = msg_body.charAt(i);
	        if ( escape(word).length > 4 ) {
	            count += 2;
	        } 
	        else {
	            count += 1;
	        }
	    }

	    if ( count > 80 && msg_type == "SMS") {
	        $('.msg_type_mms').removeClass('sms_selected');
			$('.msg_type_sms').removeClass('sms_selected');
			$('.msg_type_lms').addClass('sms_selected');
			$('.message_type').each(function() {
				if ( $(this).val() == "LMS" )
					$(this).attr("checked", true);
				else
					$(this).attr("checked", false);
				// alert( $(this).val() + ":" + $(this).attr("checked") );
			});
	        // return;
	    }
	    else if ( count <= 80 && msg_type == "LMS") {
	        $('.msg_type_mms').removeClass('sms_selected');
			$('.msg_type_sms').addClass('sms_selected');
			$('.msg_type_lms').removeClass('sms_selected');
			$('.message_type').each(function() {
				if ( $(this).val() == "SMS" )
					$(this).attr("checked", true);
				else
					$(this).attr("checked", false);
				// alert( $(this).val() + ":" + $(this).attr("checked") );
			});
	        // return;
	    }
	});
	
});
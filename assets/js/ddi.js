$(document).ready(function(){
	// datetimepicker customers
    $('.datetimepicker').datetimepicker({
    	format : 'DD-MM-YYYY HH:mm' ,
    	sideBySide: true
    });
    $('.datepicker_custom').datepicker({
    	ignoreReadonly : true
    });
	$(function(){
		$('#datetimepicker1_start').datepicker({
		  language: 'en'
		});
		$('#datetimepicker1_end').datepicker({
		  language: 'en'
		});
	    $('#datetimepicker_complete_start').datepicker({
	      language: 'en',
	    });
	    $('#datetimepicker_complete_end').datepicker({
	      language: 'en',
	    });
	    $('#datetimepicker_create_start').datepicker({
	      language: 'en',
	    });
	    $('#datetimepicker_create_end').datepicker({
	      language: 'en',
	    });
	    
	    $('#filter_data').click();
  	});
  	
	$('#print_data').click(function(){
		window.print();
	});
	$('#save,#save_approve').click(function(){
		var send_approval = $(this).attr('id') == 'save' ? '' : '&send_approval=1';
		var back_url = $(this).attr('data-back') || '';
		loading();
		var ckData = '';
		var ckId = '';
		var ckVal = '';
		$('#form1 .ckeditor').each(function(){
			ckId = $(this).attr('id');
			val = CKEDITOR.instances[ckId].getData();
			CKEDITOR.instances[ckId].updateElement();
			ckData += '&'+ckId+'='+escape(val);
		})
		if ($('#form1').parsley().validate()) {
			var disabled = $('#form1').find('input:disabled, select:disabled').removeAttr('disabled');
			$.ajax({
				url         : $('#form1').attr('action'),
				type        : "POST",
				dataType	: 'json',
				data        : $('#form1').serialize()+ckData+send_approval,
				error		: function () {
								//notify('error!');
								// clear_form_elements('#form1');
								$('#save-schedule').modal('hide')
								loadingcomplete();
							},
				success     : function(ret){
								if (ret.error==1) {
									$.gritter.add({title:page_name,text:ret.message});
									$('#save-schedule').modal('hide')
									loadingcomplete();
								}
								else{
									window.location.href=this_controller + back_url;
									clear_form_elements('#form1');
								}
								disabled.attr('disabled','disabled');
								
				}
			})
		} else {
			$('#save-schedule').modal('hide')
			loadingcomplete();
		}
		return false;
	})
	$('#save_with_file').click(function(){
		var back_url = $(this).attr('data-back') || '';
		loading();
		var ckData = '';
		var ckId = '';
		var ckVal = '';
		if ($('#form1').parsley().validate()) {
			$.ajaxFileUpload({
				url         : $('#form1').attr('action'),
				dataType	: 'json',
				secureuri		: false,
				fileElementId 	: 'file_name',
				data        : $('#form1').serializeObject(),
				error		: function () {
								//notify('error!');
								clear_form_elements('#form1');
								$('#save-schedule').modal('hide')
								loadingcomplete();
							},
				success     : function(ret){
								if (ret.error==1) {
									$.gritter.add({title:page_name,text:ret.message});
									$('#save-schedule').modal('hide')
									loadingcomplete();
								}
								else{
									window.location.href=this_controller + back_url;
									clear_form_elements('#form1');
								}
								
				}
			})
		} else {
			$('#save-schedule').modal('hide')
			loadingcomplete();
		}
		return false;
	})
	//pages, news
	$('#product_name,#title,#uri_path,#name_event').keyup(function(){
		$('#uri_path').val( convert_to_uri( $(this).val() ) );
	})
	//news
	$('#approve,#revise').click(function(){
		var proses = $(this).attr('id');
		var comment = $('#comment').val();
		if(proses == 'revise' && !comment){
			notify('comment is required','error','#approval-area','top');
			return;
		}

		if(confirm('Update to '+ proses+ ' ?')){
			loading();
			var id_news = $('#id_news').val();
			$.ajax({
				url         : this_controller+'proses_approval',
				type        : "POST",
				dataType	: 'json',
				data        : 'id_news='+id_news+'&proses='+proses+'&comment='+comment,
				error		: function () {
								notify('error!','error','#approval-area','top');
								loadingcomplete();
							},
				success     : function(ret){
								if (ret.error==1) {
									console.log(ret);
									notify(ret.message,'error','#approval-area','top');
									loadingcomplete();
								}
								else{
									window.location.href=this_controller;
								}
				}
			})		
		}
	})
	
	//frontend menu
	$('#id_frontend_menu_type').change(function(){
		var val = $(this).val();
		if(val == 1){
			$('#type_module').show();
			$('#type_extra').hide();
			$('#extra_param').attr('readonly',true);
		}
		else if(val == 2){
			$('#type_module').hide();
			$('#type_extra').show();
			$('#extra_param').attr('readonly',false);
		}
		$('#extra_param').val('');
		$('#id_module').select2('val','');		

	})
	var ajax = null;
	var ajax2 = null;
	$('#id_module').change(function(){
		var id = $(this).val();
		$('#loading-callback').show();

		if ( ajax ) { ajax.abort();}
		ajax = $.ajax({
			url         : base_url+'apps/frontend_menu/get_callback/'+id,
			error		: function () {$('#view-detail').html('error loading data, please try again');},
			success     : function(ret){
							if(ret){
								$('#view-detail').html('<div id="loading-modal"><i class="fa fa-spinner fa-spin"></i> Loading....</div>');
								$('#popViewDetail').modal('show');
								
								if ( ajax2 ) { ajax.abort();}

								ajax2 = $.ajax({
									url         : base_url+'apps/'+ret,
									type        : "POST",
									data        : '',
									error 		:function(err){
													if (err.statusText != 'abort') {
														var err_close ='<i class="icon-remove-sign icon-2x closeModal" data-dismiss="modal"></i>';
														$('#view-detail').html('Error!'+' '+err.status + ' '+err.statusText+err_close);
														$('#id_module').select2('val','');
														$('#extra_param').val('');
														$('#type_extra').hide();


													}
												},
									success     : function(ret){$('#view-detail').html(ret);}
								})
							}
							else{
								$('#extra_param').val('');
								//$('#type_extra').hide();
							}
							$('#loading-callback').hide();

			}
		});
	})

	$('#reply-contactus').click(function(){
		if ($('#form1').parsley().validate()) {
			var id = $(this).attr('data-id');
			loading();
			$.ajax({
				url         : this_controller+'reply',
				type        : "POST",
				dataType	: 'json',
				data        : $('#form1').serialize(),
				error		: function () {
								$.gritter.add({title:page_name,text:'Please try again'});
								$('#popDetail').modal('hide');
								loadingcomplete();
							},
				success     : function(ret){
								if(ret.error == 1){
									$.gritter.add({title:page_name,text:'message not. '+ret.message});
								}
								else{
									$.gritter.add({title:page_name,text:'Message Sent'});
									
									$('#grid1 .reload').trigger('click');
								}
								$('#popDetail').modal('hide');
								the_grid('grid1',this_controller+'records');
								loadingcomplete();
				}
			});
		} else {
			loadingcomplete();
		}
		return false;
	});
	$('#multiple_delete').click(function(){
		if(confirm('Hapus Data ?')){
			var send_approval = $(this).attr('id') == 'save' ? '' : '&send_approval=1';
			var back_url = $(this).attr('data-back') || '';
			loading();
			var ckData = '';
			var ckId = '';
			var ckVal = '';
			$('.ckeditor').each(function(){
				ckId = $(this).attr('id');
				val = CKEDITOR.instances[ckId].getData();
				ckData += '&'+ckId+'='+escape(val);
			})
			$.ajax({
				url         : $('#form1').attr('action'),
				type        : "POST",
				dataType	: 'json',
				data        : $('#form1').serialize()+ckData+send_approval,
				error		: function () {
								notify('error!');
								loadingcomplete();
							},
				success     : function(ret){
								if (ret.error==1) {
									console.log(ret);
									$.gritter.add({title:page_name,text:ret.message});
									loadingcomplete();
								}
								else{
									window.location.href=this_controller + back_url;
								}
				}
			});
		}
	})

})
//frontend menu
$(document).on('click','.select-data',function(){
	var url = $(this).attr('data-url');
	$('#extra_param').val(url);
	$('#popViewDetail').modal('hide');
	$('#change-data').show();
	$('#type_extra').show();

})

//news
var ajx = null;
$(document).on('click','.view-detail',function(){
	var id = $(this).attr('data-id');
	$('#popViewDetail').modal('show');
	$('#view-detail').html('<div id="loading-modal"><i class="fa fa-spinner fa-spin"></i> Loading....</div>');
	if ( ajx ) {ajx.abort()};
	ajx = $.ajax({
			url         : base_url+'apps/news/version_detail/'+id,
			type        : "POST",
			data        : '',
			error		: function () {$('#view-detail').html('error loading data, please try again');},
			success     : function(ret){$('#view-detail').html(ret);}
		})
})
//view detail contact us
function convert_to_uri(val){
    return val
        .toLowerCase()
        .replace(/ /g,'-')
        .replace(/[^\w-]+/g,'')
        ;
}

var ajax_contact;
$(document).on('click','.detail-hubungi-kami',function(){
	$('#message-reply').val('');
	$('#id-reply').val($(this).attr('data-id'));
	$('#popDetail').modal('show');
	var btn = $(this);
    $('#loading-contactus').show();
    if(ajax_contact){
    	ajax_contact.abort();
    }
    var ajax_contact = $.ajax({
							url         : this_controller+'detail/'+btn.attr('data-id'),
							error		: function () {
											$('#loading-contactus').hide();
											$('#content-contactus').html('error!<br>Please try again');

										},
							success     : function(ret){
											$('#loading-contactus').hide();
											$('#content-contactus').html(ret);

							}
						})
})
function export_to_excel(type){
	if(type == 1) {
		$.ajax({
			type : 'POST',
			url : $('#export_to_excel_form').attr('action'),
			data : $('#export_to_excel_form').serialize(),
			success : function(){
				alert('Terima Kasih. Cek email Anda untuk mengunduh file.');
			}
		})
	} else {
		$('#export_to_excel_form').submit();
	}
}

var ajax_customer_quote;
$(document).on('click','.detail-customer-quote',function(){
	$('#popDetailCustomerQuote').modal('show');
	var btn = $(this);
    $('#loading-customer-quote').show();
    if(ajax_customer_quote){
    	ajax_customer_quote.abort();
    }
    var ajax_customer_quote = $.ajax({
							url         : this_controller+'detail/'+btn.attr('data-id'),
							error		: function () {
											$('#loading-customer-quote').hide();
											$('#content-customer-quote').html('error!<br>Please try again');

										},
							success     : function(ret){
											$('#loading-customer-quote').hide();
											$('#content-customer-quote').html(ret);
										}
							})
})
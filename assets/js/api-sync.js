$(document).on('click','.sync',function(){
	$('#icon-sync').addClass('fa-spin');
	data_url_save = $(this).attr('data-url-save');
	page_name = $(this).attr('data-page-name');
	error = $(this).attr('data-message-error');
	success = $(this).attr('data-message-success');
	loading();
	$.ajax({
		url         : url_api_mitra_plus,
		dataType	: 'json',
		error		: function () {
			hide_icon_sync();
			$.gritter.add({title:page_name,text:error});
		},
		data: {
			  "funcName":$(this).attr('data-url-api'),
	  	},
	  	type: "POST",
		success     : function(ret){
			$.ajax({
				url         : data_url_save,
				type        : "POST",
				dataType	: 'json',
				data        : ret,
				error		: function () {
								hide_icon_sync();
								$.gritter.add({title:page_name,text:error});
				},
				success     : function(retR){
								hide_icon_sync();
								$.gritter.add({title:page_name,text:success});
								re_generate_data();

				}
			})

		}
	})
})

function hide_icon_sync(){
	loadingcomplete();
	setTimeout(function(){
  		$('#icon-sync').removeClass('fa-spin');
	}, 1000);
}
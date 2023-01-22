$(document).on('click','#btn_process',function(){
	if ($('#form_questionnaire').parsley().validate()) {
		var btn = $(this);
		btn.text('loading...')
		btn.attr('disabled','disabled');
		$.ajax({
			url      : base_url+'event_detail/process_questionnaire',
			data     : $('#form_questionnaire').serialize(),
			type     : 'post',
			dataType : 'json',
		}).done(function(ret){
			btn.text('Kirim')
			btn.removeAttr('disabled');
			if (!ret.error) {
				clear_form_elements('#form_questionnaire');
				swalAlert(ret.msg);
			} else {
				swalAlert(ret.msg,'error');
			}
		}).fail(function(ret){
			btn.removeAttr('disabled');
			alert('error');
		});
	}
});
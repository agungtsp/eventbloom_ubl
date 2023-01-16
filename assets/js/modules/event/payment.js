$(document).ready(function(){
	$('form .number_format').number(true, 0,',','.');

	if ($('#id_ref_status_payment').val() == 2) { // 2=paid
		$('#form1 :input, #save').attr('disabled','disabled');
	}

	$('#id_ref_bank').change();
});

$(document).on('change', '#id_ref_bank', function () {
	$('#bank_description').addClass('invis');

	var id_bank = $(this).val();
	
	if (id_bank) {
		$.ajax({
		  url: this_controller+'get_bank_info',
		  type: "GET",
		  data: {
			id_bank : id_bank
		  },
		  dataType: "json"
		}).
		done(function(desc) {
			$('#bank_description').removeClass('invis');
			$('#bank_description .well').html(desc);
		}).
		fail(function(jqXHR, textStatus) {
			alert( "Request failed: " + textStatus );
		});
	}
});
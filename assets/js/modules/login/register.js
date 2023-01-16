$(document).ready(function(){
	$('#birthdate').datepicker({
		endDate : new Date(),
		startView : 'decade'
	});
});

function loading(){
    $('#page-loader').removeClass('hide').show()
}
function loadingcomplete(){
    $('#page-loader').addClass('hide').hide()
}
$('#register').click(function(){
	loading();
	if ($('#form-register').parsley().validate()) {
		$.ajax({
			url         : $('#form-register').attr('action'),
			type        : "POST",
			dataType	: 'json',
			data        : $('#form-register').serialize(),
			error		: function () {
							loadingcomplete();
						},
			success     : function(ret){
				if (ret.error==1) {
					location.reload();
					// loadingcomplete();
				}
				else{
					window.location = base_url+"apps/login";
				}
							
			}
		})
	} else {
		loadingcomplete();
	}
	return false;
});

$(document).ready(function(){
	$('select').select2();
	$('#kode_ref_negara').change();
});

$(document).on('change','#kode_ref_negara',function(){
	$('.indonesia_address').addClass('hidden');

	if ($(this).val() == 'ID') { // Indonesia
		$('.indonesia_address').removeClass('hidden');
	}
});

$(document).on('change','#kode_ref_provinsi',function(){
	$('#kode_ref_kabupaten').html('<option value="">Sedang memuat...</option>');
	$('#kode_ref_kecamatan,#kode_ref_kelurahan').empty().attr('disabled','disabled');
	$('select').select2();
	
	var kode_provinsi = $(this).val();

	$.ajax({
	  url: this_controller+'get_kabupaten',
	  type: "GET",
	  data: {code : kode_provinsi},
	  dataType: "json"
	}).
	done(function(ret) {
		$('#kode_ref_kabupaten').html(ret.list_data);
		$('#kode_ref_kabupaten').removeAttr('disabled');
		$('select').select2();
	}).
	fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});
});

$(document).on('change','#kode_ref_kabupaten',function(){
	$('#kode_ref_kecamatan').html('<option value="">Sedang memuat...</option>');
	$('#kode_ref_kelurahan').empty().attr('disabled','disabled');
	$('select').select2();
	
	var kode_kabupaten = $(this).val();

	$.ajax({
	  url: this_controller+'get_kecamatan',
	  type: "GET",
	  data: {code : kode_kabupaten},
	  dataType: "json"
	}).
	done(function(ret) {
		$('#kode_ref_kecamatan').html(ret.list_data);
		$('#kode_ref_kecamatan').removeAttr('disabled');
		$('select').select2();
	}).
	fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});
});

$(document).on('change','#kode_ref_kecamatan',function(){
	$('#kode_ref_kelurahan').html('<option value="">Sedang memuat...</option>');
	$('select').select2();
	
	var kode_kecamatan = $(this).val();

	$.ajax({
	  url: this_controller+'get_kelurahan',
	  type: "GET",
	  data: {code : kode_kecamatan},
	  dataType: "json"
	}).
	done(function(ret) {
		$('#kode_ref_kelurahan').html(ret.list_data);
		$('#kode_ref_kelurahan').removeAttr('disabled');
		$('select').select2();
	}).
	fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});
});
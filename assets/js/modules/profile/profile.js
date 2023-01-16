jQuery(document).ready(function(){
	$('#birthdate').datepicker({
		endDate : new Date(),
		startView : 'decade'
	});
	// binds form submission and fields to the validation engine
	jQuery("#form1").validationEngine();
});	
$('#save').click(function(){
	if( $("#form1").validationEngine('validate') == true){
		var param = $('#form1').serialize();
		var url   = this_controller+'/proses';
		$.ajax({
				url         : url,
				type        : "POST",
				data        : param,
				beforeSend  : function(){$('#save').html('Loading...');},
				success     : function(msg){
								if(msg == 'err_email'){
									$('#email').validationEngine('showPrompt', 'Email sudah digunakan oleh User lain','');
								} else {
									$.gritter.add({title:page_name, text: msg});
								}	
								$('#save').html('Save');
														   }
		});
	}
});

$(document).ready(function(){
	$('#kode_ref_negara').change();
	
	if (kode_ref_provinsi) {
		$('#kode_ref_provinsi').change();
	}
});

$(document).on('change','#kode_ref_negara',function(){
	$('.indonesia_address').addClass('hidden');

	if ($(this).val() == 'ID') { // Indonesia
		$('.indonesia_address').removeClass('hidden');
	}
});

var krp = 0;
$(document).on('change','#kode_ref_provinsi',function(){
	$('#kode_ref_kabupaten').html('<option value="">Sedang memuat...</option>');
	$('#kode_ref_kecamatan,#kode_ref_kelurahan').empty().attr('disabled','disabled');
	$('select').select2();
	
	var kode_kabupaten = (krp==0) ? kode_ref_kabupaten : '';
	var kode_provinsi = $(this).val();

	$.ajax({
	  url: this_controller+'get_kabupaten',
	  type: "GET",
	  data: {code : kode_provinsi, selected : kode_kabupaten},
	  dataType: "json"
	}).
	done(function(ret) {
		$('#kode_ref_kabupaten').html(ret.list_data);
		$('#kode_ref_kabupaten').removeAttr('disabled');
		$('select').select2();

		if (kode_kabupaten) {
			$('#kode_ref_kabupaten').change();
		}
	}).
	fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});

	krp++;
});

var krkab = 0;
$(document).on('change','#kode_ref_kabupaten',function(){
	$('#kode_ref_kecamatan').html('<option value="">Sedang memuat...</option>');
	$('#kode_ref_kelurahan').empty().attr('disabled','disabled');
	$('select').select2();
	
	var kode_kecamatan = (krkab==0) ? kode_ref_kecamatan : '';
	var kode_kabupaten = $(this).val();

	$.ajax({
	  url: this_controller+'get_kecamatan',
	  type: "GET",
	  data: {code : kode_kabupaten, selected : kode_kecamatan},
	  dataType: "json"
	}).
	done(function(ret) {
		$('#kode_ref_kecamatan').html(ret.list_data);
		$('#kode_ref_kecamatan').removeAttr('disabled');
		$('select').select2();

		if (kode_kecamatan) {
			$('#kode_ref_kecamatan').change();
		}
	}).
	fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});

	krkab++;
});

var krkec = 0;
$(document).on('change','#kode_ref_kecamatan',function(){
	$('#kode_ref_kelurahan').html('<option value="">Sedang memuat...</option>');
	$('select').select2();
	
	var kode_kelurahan = (krkec==0) ? kode_ref_kelurahan : '';
	var kode_kecamatan = $(this).val();

	$.ajax({
	  url: this_controller+'get_kelurahan',
	  type: "GET",
	  data: {code : kode_kecamatan, selected : kode_kelurahan},
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

	krkec++;
});
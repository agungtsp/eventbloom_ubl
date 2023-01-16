$(document).ready(function(){
	$('#birthdate').datepicker({
		endDate : new Date(),
		startView : 'decade'
	});
});

function change_pwd(st){ 
	if(st == true){
		$('#userpass').removeClass("hidden");
		$('#userpass').show();
	}
	else{
		$('#userpass').addClass("hidden");
		$('#userpass').hide();
	}
}

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
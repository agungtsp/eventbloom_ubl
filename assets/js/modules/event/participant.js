$(document).ready(function(){
	if (id_group == 4 && !is_editable) {
		$('form :input').attr('disabled','disabled');
		$('form select').select2();
	}

	$('#kode_ref_negara').change();
	
	if (kode_provinsi) {
		$('#kode_ref_provinsi').change();
	}
});

$(document).on('change','#id_auth_user',function(){
	var id_auth_user = $(this).val();

	loading();
	$.ajax({
	  url: this_controller+'get_detail_participant',
	  type: "GET",
	  data: {
		id : id_auth_user,
		id_event : id_event
	  },
	  dataType: "json"
	}).
	done(function(ret) {
		if (!ret.error) {
			$('#fullname').val(ret.username);
			$('#email').val(ret.email);
			$('[name=gender][value='+ret.gender+']').prop('checked',true);
			$('#phone').val(ret.phone);

			kode_provinsi  = ret.kode_ref_provinsi;
			kode_kabupaten = ret.kode_ref_kabupaten;
			kode_kecamatan = ret.kode_ref_kecamatan;
			kode_kelurahan = ret.kode_ref_kelurahan;

			$('#kode_ref_negara').val(ret.kode_ref_negara).change();
			$('#kode_ref_provinsi').val(ret.kode_ref_provinsi).change();
			$('#postal_code').val(ret.postal_code);
			$('#address').val(ret.address);
			$('#facebook').val(ret.facebook);
			$('#instagram').val(ret.instagram);
			$('#twitter').val(ret.twitter);
			$('#website').val(ret.website);
			$('#description').val(ret.description);
			$('#birthdate').val(ret.birthdate);
			$('#sub_price').val(ret.sub_price);
			get_event_price();
		} else {
			alert(ret.msg);
		}

		loadingcomplete();
	}).
	fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});
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
	$('form select').select2();
	
	kode_kabupaten = ((krp==0 && is_edit) || kode_kabupaten) ? kode_kabupaten : '';
	kode_provinsi = $(this).val();

	$.ajax({
	  url: this_controller+'get_kabupaten',
	  type: "GET",
	  data: {code : kode_provinsi, selected : kode_kabupaten},
	  dataType: "json"
	}).
	done(function(ret) {
		$('#kode_ref_kabupaten').html(ret.list_data);
		$('#kode_ref_kabupaten').removeAttr('disabled');
		$('form select').select2();

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
	$('form select').select2();
	
	kode_kecamatan = ((krkab==0 && is_edit) || kode_kecamatan) ? kode_kecamatan : '';
	kode_kabupaten = $(this).val();

	$.ajax({
	  url: this_controller+'get_kecamatan',
	  type: "GET",
	  data: {code : kode_kabupaten, selected : kode_kecamatan},
	  dataType: "json"
	}).
	done(function(ret) {
		$('#kode_ref_kecamatan').html(ret.list_data);
		$('#kode_ref_kecamatan').removeAttr('disabled');

		if (id_group == 4 && !is_editable) {
			$('form select').attr('disabled','disabled');
		}

		$('form select').select2();

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
	$('form select').select2();
	
	kode_kelurahan = ((krkec==0 && is_edit) || kode_kelurahan) ? kode_kelurahan : '';
	kode_kecamatan = $(this).val();

	$.ajax({
	  url: this_controller+'get_kelurahan',
	  type: "GET",
	  data: {code : kode_kecamatan, selected : kode_kelurahan},
	  dataType: "json"
	}).
	done(function(ret) {
		$('#kode_ref_kelurahan').html(ret.list_data);
		$('#kode_ref_kelurahan').removeAttr('disabled');

		if (id_group == 4 && !is_editable) {
			$('form select').attr('disabled','disabled');
		}

		$('form select').select2();
	}).
	fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});

	krkec++;
});

$(document).on('click','#is_early_bird',function(){
	get_event_price();
});

function get_event_price() {
	var is_early_bird = $('#is_early_bird').is(':checked') ? 1 : 0;
	
	$.ajax({
	  url: this_controller+'event_price',
	  type: "GET",
	  data: {
		is_early_bird : is_early_bird,
		birthdate : $('#birthdate').val(),
		id_event : id_event
	  },
	  dataType: "json"
	}).
	done(function(price) {
		$('#sub_price').val(parseFloat(price));
		get_total_price();
	}).
	fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});
}

function get_total_price() {
	var total_price = 0;
	$("form .sub_price").each(function(){
		total_price += ($(this).val()) ? parseFloat(($(this).val()).replace(/\./g,'')) : 0;
	});

	$('form #total_price').val(total_price);
	$('form .number_format').number(true, 0,',','.');
}

/* Start of Event Workshop */

// multiple add
var numDataWorkshop 	= $('form .cloneInputDataWorkshop').length;
$(document).on('click','#btnAddWorkshop', function (){
	numDataWorkshop++;
	$('#workshop_additional_form').append($("#workshop_form_template").html().replace(/__NO_IMG_IMAGE/g,numDataWorkshop));

	$('.number_format').number(true, 0,',','.');
	$('form select').select2();
});


// multiple delete
$(document).on('click', '.del-data-workshop', function () {
	if (confirm("Anda yakin ingin menghapus data ini? Data yang telah dihapus tidak dapat dikembalikan.")){
		$(this).closest('.cloneInputDataWorkshop').slideUp('slow',function(){
			$(this).empty();
		});
	}
	return false;
});

$(document).on('change', '.workshop_id_ref_event_workshop', function () {
	var section       = $(this).closest('.cloneInputDataWorkshop');
	// var is_early_bird = section.find('.workshop_is_early_bird').is(':checked') ? 1 : 0;
	var is_early_bird = $('#is_early_bird').is(':checked') ? 1 : 0;
	var id_workshop   = $(this).val();

	$.ajax({
	  url: this_controller+'workshop_price',
	  type: "GET",
	  data: {
		is_early_bird : is_early_bird,
		id_workshop : id_workshop,
		id_event : id_event
	  },
	  dataType: "json"
	}).
	done(function(price) {
		section.find('.sub_price').val(parseFloat(price));
		get_total_price();
	}).
	fail(function(jqXHR, textStatus) {
		alert( "Request failed: " + textStatus );
	});
});

$(document).on('click','.workshop_is_early_bird',function(){
	$(this).closest('.cloneInputDataWorkshop').find('.workshop_id_ref_event_workshop').change();
});

/* End of Event Workshop */
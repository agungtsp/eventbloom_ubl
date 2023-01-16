$(document).ready(function(){
	$('.number_format').number(true, 0,',','.');

	$("#geocomplete").geocomplete({
		map: ".map_canvas",
		mapOptions: {
			zoom: 18
			},
		details: "form",
		types: ["geocode", "establishment"],
		markerOptions: {
						draggable: true
					},
		location: [$("#latitude").val(), $("#longitude").val()]
	});
	
	$("#geocomplete").bind("geocode:dragged", function(event, latLng){
		$("#latitude").val(latLng.lat());
		$("#longitude").val(latLng.lng());
	});
});

$(document).on('click','#is_private',function(){
	$('#password').closest('.form-group').addClass('hidden');
	// $('#password').removeAttr('data-parsley-required');

	if ($(this).is(':checked')) {
		$('#password').closest('.form-group').removeClass('hidden');
		// $('#password').attr('data-parsley-required', 'true');
	}
});

/* Start of Event Image */

// multiple add
var numEventImage 	= $('form .cloneInputEventImage').length;
$(document).on('click','#btnAddEventImage',function (){
	numEventImage++;
	$('#image_additional_form').append($("#image_form_template").html().replace(/__NO_IMG_IMAGE/g,numEventImage));
});


// multiple delete
$(document).on('click', '.del-event-image', function () {
	if (confirm("Anda yakin ingin menghapus gambar ini? Gambar yang telah dihapus tidak dapat dikembalikan.")){
		$(this).closest('.cloneInputEventImage').slideUp('slow',function(){
			$(this).empty();
		});
	}
	return false;
});

/* End of Event Image */

/* Start of Event Speaker */

// multiple add
var numDataSpeaker 	= $('form .cloneInputDataSpeaker').length;
$(document).on('click','#btnAddSpeaker',function (){
	numDataSpeaker++;
	$('#speaker_additional_form').append($("#speaker_form_template").html().replace(/__NO_IMG_IMAGE/g,numDataSpeaker).replace(/\[0\]/g,'['+numDataSpeaker+']'));

	$('.number_format').number(true, 0,',','.');
});


// multiple delete
$(document).on('click', '.del-data-speaker', function () {
	if (confirm("Anda yakin ingin menghapus data ini? Data yang telah dihapus tidak dapat dikembalikan.")){
		$(this).closest('.cloneInputDataSpeaker').slideUp('slow',function(){
			$(this).empty();
		});
	}
	return false;
});

/* End of Event Speaker */

/* Start of Event Workshop */

// multiple add
var numDataWorkshop 	= $('form .cloneInputDataWorkshop').length;
$(document).on('click','#btnAddWorkshop',function (){
	numDataWorkshop++;
	$('#workshop_additional_form').append($("#workshop_form_template").html().replace(/__NO_IMG_IMAGE/g,numDataWorkshop).replace(/\[0\]/g,'['+numDataWorkshop+']'));

	$('.number_format').number(true, 0,',','.');
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

/* End of Event Workshop */

/* Start of Event Pricing */

// multiple add
var numDataPricing 	= $('form .cloneInputDataPricing').length;
$(document).on('click','.btnAddPricing',function (){
	is_early_bird  = $(this).attr('data-is-early-bird');
	div_additional = (is_early_bird == 0) ? '#pricing_normal_additional_form' : '#pricing_additional_form';

	numDataPricing++;
	$(div_additional).append($("#pricing_form_template").html().replace(/__NO_IMG_IMAGE/g,numDataPricing));
	$('form [name="data_pricing['+numDataPricing+'][is_early_bird]"]').val(is_early_bird);

	$('.number_format').number(true, 0,',','.');
	$('.datepicker_custom').datepicker();
});


// multiple delete
$(document).on('click', '.del-data-pricing', function () {
	if (confirm("Anda yakin ingin menghapus data ini? Data yang telah dihapus tidak dapat dikembalikan.")){
		$(this).closest('.cloneInputDataPricing').slideUp('slow',function(){
			$(this).empty();
		});
	}
	return false;
});

/* End of Event Workshop */
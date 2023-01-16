$(document).on('click','.approve_agent',function(){
	var func = $(this).attr('data-func');
	var id = $(this).attr('id');
	loading();
	$.ajax({
		url			: this_controller+func,
		type 		: "POST",
		dataType: 'json',
		data    : 'id='+id,
		error		: function () {
							$.gritter.add({title:page_name,text:'Please try again'});
							loadingcomplete();
						},
		success	: function(ret){
							if(ret.error == 1){
								$.gritter.add({title:page_name,text:ret.message});
							}
							else{
								$.gritter.add({title:page_name,text:ret.message});
								
								$('#grid1 .reload').trigger('click');
							}
							the_grid('grid1',this_controller+'records');
							loadingcomplete();
						}
	});
});
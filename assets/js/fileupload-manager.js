
function set_file_upload(id,targetid){
    $('#'+id).popupWindow({ 
        windowURL:base_url+'Elfinder_upload/elfinder_popup_data/'+targetid, 
        windowName:'Filebrowser',
        height:490, 
        width:950,
        centerScreen:1
    }); 
	$('#'+id+"_clear").click(function(e) {
	    $('#'+ targetid).val("");
	    $('#'+ targetid+'_text').html("");
	});

}
function processFile(targetid,file){
    $('#'+ targetid).val(file);
    $('#'+ targetid+'_text').html("<a href='"+file+"' target='_BLANK'>"+file+"</a>");
}


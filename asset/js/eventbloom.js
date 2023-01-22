function clear_form_elements(ele) {
  var kelas;
  $(ele).find(':input').each(function() {
    kelas = $(this).attr('class');
    if(!$(this).hasClass('no_clear')){ 
      switch(this.type) {
        case 'password':
        case 'select-multiple':
        case 'select-one':
          $("#"+$(this).attr('id')).val($("#"+$(this).attr('id')+" option:first").val());
          break;
        case 'text':
        case 'email':
        case 'file':
        case 'hidden':
        case 'textarea':
            $(this).val('');
            break;
        case 'checkbox':
        case 'radio':
          if ($(this).attr('name')!='choose_for' && $(this).attr('name')!='choose_for_checkbox') {
            $('[name="'+$(this).attr('name')+'"]').prop('checked', false);
          }
          break;
        case 'range':
          $(this).val($(this).attr('min')).change();
          break;
        case 'select':
          $(this).val('');
          $(this).selectpicker('val','');
          break;
      }       
    }
      
  });
}
function swalAlert(text,type) {
  type     = type ? type : 'success';//success//info//warning//error
  swal('',text,type).then(function(){ 
    location.reload();
  });
}
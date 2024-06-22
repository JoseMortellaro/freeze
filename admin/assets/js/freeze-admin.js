jQuery(document).ready(function($){
  $('#freeze-rename-submit').on('click',function(){
    var section = $('#freeze-section'),
      succ_el = $('#freeze-message-success'),
      no_access_el = $('#freeze-message-no-access'),
      error_el = $('#freeze-message-fail');
    section.addClass('freeze-progress');
    succ_el.addClass('freeze-hidden');
    error_el.addClass('freeze-hidden');
    $.ajax({
      type : "POST",
      url : ajaxurl,
      data : {
        "nonce" : $("#eos_freeze_rename_nonce").val(),
        "new_name" : $('#freeze-folder-name').val(),
        "action" : 'eos_freeze_rename_folder'
      },
      success : function(response){
        if('_no_file_access' === response){
          no_access_el.removeClass('freeze-hidden');
        }
        else if (parseInt(response) == 1) {
          succ_el.removeClass('freeze-hidden');
        }
        else{
          error_el.removeClass('freeze-hidden');
        }
        section.removeClass('freeze-progress');
      }
    });
  });
});

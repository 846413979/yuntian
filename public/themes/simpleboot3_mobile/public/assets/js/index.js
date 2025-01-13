$(document).ready(function (){
    $(document).on('click','#inquiry',function (){
        $('#feedback_type').val(1);
        $('.popover_wrap').show();
    })

    $(document).on('click','#download',function (){
        $('#feedback_type').val(1);
        $('#file').val($(this).data('href'));
        $('.popover_wrap').show();
    })
})
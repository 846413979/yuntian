$(document).ready(function() {
    $('.choose-list .choose-item').hover(function (){
        $(this).addClass('active')
        $(this).find('.choose-item-icon img').addClass('rotated').attr('src',$(this).find('.choose-item-icon img').data('active_img'))
    },function (){
        $(this).removeClass('active')
        $(this).find('.choose-item-icon img').removeClass('rotated').attr('src',$(this).find('.choose-item-icon img').data('default_img'))
    })

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
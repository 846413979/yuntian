$(document).ready(function() {
    $('.choose-list .choose-item').hover(function (){
        $(this).addClass('active')
        $(this).find('.choose-item-icon img').addClass('rotated').attr('src',$(this).find('.choose-item-icon img').data('active_img'))
    },function (){
        $(this).removeClass('active')
        $(this).find('.choose-item-icon img').removeClass('rotated').attr('src',$(this).find('.choose-item-icon img').data('default_img'))
    })
})
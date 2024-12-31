$(document).ready(function(){

    /***** 语言切换 *****/
    var isRotated = false;
    $('.header-up-right-item-lang').click(function (){
        if (isRotated) {
            $(this).find('.header-up-right-item-lang-dropdown').hide()
            $(this).find('.header-up-right-item-lang-dropdown-icon').css('transform', 'rotate(0deg)');
        } else {
            $(this).find('.header-up-right-item-lang-dropdown').show()
            $(this).find('.header-up-right-item-lang-dropdown-icon').css('transform', 'rotate(180deg)');
        }
        isRotated = !isRotated;
    })


    /****** 悬浮框鼠标移入效果 ********/
    $('.right_fix .right_fix_box').hover(function (){
        $(this).addClass('active')
        $(this).find('img').attr('src',$(this).find('img').data('active_img'))
    }, function (){
        $(this).removeClass('active')
        $(this).find('img').attr('src',$(this).find('img').data('default_img'))
    })

})
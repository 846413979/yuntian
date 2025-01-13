// script.js
$(document).ready(function() {
    $('.info_right_box .info_right_item_checkbox .info_right_item_content').on('click','.info_right_item_content_level',function (){
        $(this).addClass('active').siblings().removeClass('active');
    })

    //可配吊具
    $('.info_available .info_available_list .info_available_list_item').click(function (){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            $(this).find('img.available_img').show();
            $(this).find('img.available_img_active').hide();
        }else{
            $(this).addClass('active');
            $(this).find('img.available_img').hide();
            $(this).find('img.available_img_active').show();
        }
    })


    // 描述
    $('.info_content .info_content_title .info_content_item').click(function (){
        var n = $(this).index();
        if($(this).hasClass('active')){
            return;
        }
        $(this).addClass('active').siblings().removeClass('active');
        $('.info_content_content .info_content_content_item').eq(n).show().siblings().hide();

    })

    $('#inquiry').click(function (){
        $('.popover_wrap').show();
    })

    $('#download').click(function (){
        $('#file').val($(this).data('href'));
        $('.popover_wrap').show();
    })

    $('.popover_wrap .popover_container .popover_close').click(function (){
        $('#file').val('');
        $('.popover_wrap').hide();
    })


    // 产品图片
    var $mainImg = $('#main-img');
    var $thumbnails = $('.thumbnail-gallery-item');
    var $thumbnailGallery = $('.thumbnail-gallery-list');
    var $prevBtn = $('.prev-btn');
    var $nextBtn = $('.next-btn');

    var currentIndex = 0;

    function updateMainImage(index) {
        $mainImg.attr('src', $thumbnails.eq(index).data('main-src'));
    }

    function updateThumbnailSelection() {
        $thumbnails.removeClass('active').eq(currentIndex).addClass('active');
    }

    $thumbnails.on('click', function() {
        currentIndex = $thumbnails.index(this);
        updateMainImage(currentIndex);
        updateThumbnailSelection();
    });

    $prevBtn.on('click', function() {
        currentIndex = (currentIndex - 1 + $thumbnails.length) % $thumbnails.length;
        $thumbnailGallery.scrollLeft(currentIndex * $thumbnails.eq(0).width());
        updateMainImage(currentIndex);
        updateThumbnailSelection();
    });

    $nextBtn.on('click', function() {
        currentIndex = (currentIndex + 1) % $thumbnails.length;
        $thumbnailGallery.scrollLeft(currentIndex * $thumbnails.eq(0).width());
        updateMainImage(currentIndex);
        updateThumbnailSelection();
    });

    // 初始化
    updateMainImage(currentIndex);
    updateThumbnailSelection();

    $prevBtn.hover(function() {
        $('.prev-img').hide()
        $('.prev-img-active').show()
    }, function() {
        $('.prev-img').show()
        $('.prev-img-active').hide()
    })

    $nextBtn.hover(function() {
        $('.next-img').hide()
        $('.next-img-active').show()
    }, function() {
        $('.next-img').show()
        $('.next-img-active').hide()
    })

    $('.info_btn img').hover(function (){
        var active_img = $(this).data('active_img');
        $(this).attr('src',active_img);
    },function (){
        var unactive_img = $(this).data('unactive_img');
        $(this).attr('src',unactive_img);
    })

    $('#inquiry').click(function (){
        $('#feedback_type').val(1);
        $('.popover_wrap').show();
    })

    $('#download').click(function (){
        $('#feedback_type').val(1);
        $('#file').val($(this).data('href'));
        $('.popover_wrap').show();
    })

    // 热门产品
    $('.product-line ul li').click(function (){
        let index = $(this).index();
        $('.related_products_list ul').animate({'marginLeft':-1440*index+'px'});
        $(this).addClass('active').siblings().removeClass('active');
    })

    $(document).on('click','#inquiry1',function (){
        $('#feedback_type').val(1);
        $('.popover_wrap').show();
    })

    $(document).on('click','#download1',function (){
        $('#feedback_type').val(1);
        $('#file').val($(this).data('href'));
        $('.popover_wrap').show();
    })
});
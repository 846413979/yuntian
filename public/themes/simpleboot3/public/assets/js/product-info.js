$(document).ready(function () {
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

    $thumbnails.on('click', function () {
        currentIndex = $thumbnails.index(this);
        updateMainImage(currentIndex);
        updateThumbnailSelection();
    });

    $prevBtn.on('click', function () {
        currentIndex = (currentIndex - 1 + $thumbnails.length) % $thumbnails.length;
        $thumbnailGallery.scrollLeft(currentIndex * $thumbnails.eq(0).width());
        updateMainImage(currentIndex);
        updateThumbnailSelection();
    });

    $nextBtn.on('click', function () {
        currentIndex = (currentIndex + 1) % $thumbnails.length;
        $thumbnailGallery.scrollLeft(currentIndex * $thumbnails.eq(0).width());
        updateMainImage(currentIndex);
        updateThumbnailSelection();
    });

    // 初始化
    updateMainImage(currentIndex);
    updateThumbnailSelection();

    $prevBtn.hover(function () {
        $('.prev-img').hide()
        $('.prev-img-active').show()
    }, function () {
        $('.prev-img').show()
        $('.prev-img-active').hide()
    })

    $nextBtn.hover(function () {
        $('.next-img').hide()
        $('.next-img-active').show()
    }, function () {
        $('.next-img').show()
        $('.next-img-active').hide()
    })


    $('.info_right_box .info_right_item_checkbox .info_right_item_content').on('click','.info_right_item_content_level',function (){
        $(this).addClass('active').siblings().removeClass('active');
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
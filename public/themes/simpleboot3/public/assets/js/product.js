$(document).ready(function(){
    /***** 产品分类展开收缩 *****/
    $('.category_item').click(function (){
        let isRotated = $(this).hasClass('active')
        $(this).find('.category_item_list').slideToggle()
        $(this).siblings().find('.category_item_list').slideUp()
        $(this).siblings().removeClass('active')
        $(this).toggleClass('active')
        $('.category_item_list_item').removeClass('active')

        if (!isRotated) {
            change();
        }

    })

    /***  子分类点击  ***/
    $('.category_item_list_item').click(function (event){
        event.stopPropagation()
        if($(this).hasClass('active')){
            return
        }
        $('.category_item_list_item').removeClass('active')
        $(this).addClass('active')

        change();
    })

    /*** 行业案例点击 ***/
    $('.industries_item').click(function (){
        $(this).toggleClass('active')

        change();
    })

    /*** 搜索框点击 ***/
    $('#product_search').click(function (){
        change();
    })


    // ajax请求列表数据
    function change() {
        let industry = [];
        $('.industries_list .industries_item.active').each(function () {
            industry.push($(this).data('id'))
        })

        const data = {
            keywords: $('.search_input').val(),
            profession_ids: industry.join(',')
        };

        let url = $('.category_item.active').data('url');
        if ($('.category_item_list_item').hasClass('active')) {
            url = $('.category_item_list_item.active').data('url');
        }
        if(!url){
            url = $('.category_list').data('url');
        }


        $.ajax({
            url: url,
            type: 'get',
            data: data,
            dataType: 'json',
            success: function (res) {
                if (res.code === 0) {
                    alert(res.msg)
                }
                let list = res.data.list.data
                var html = '';
                for (var i = 0; i < list.length; i++) {
                    html += '<li class="product_item">' +
                        '<a href="' + list[i].url + '">' +
                        '<div class="product_list_item_img">' +
                        '<img src="' + list[i].thumbnail + '" alt="' + list[i].title + '">' +
                        '</div>' +
                        '<div class="product_list_item_title">' +
                        list[i].title +
                        '</div>' +
                        '</a>' +
                        '<div class="product_list_item_btn">' +
                        '<div class="product_list_item_btn_submit" id="inquiry">SUBMIT</div>';
                    if (list[i].file.length!==0){
                        html += '<div class="product_list_item_btn_download" id="download" data-href="'+list[i].file.url+'">DOWNLOAD</div>';
                    }
                    html += '</div>' +
                        '</li>';
                }
                $('.product_list').html(html)

                $('.pagination').html(res.data.page)
            }
        })
    }


    $(document).on('click','#inquiry',function (){
        $('#feedback_type').val(1);
        $('.popover_wrap').show();
    })

    $(document).on('click','#download',function (){
        $('#feedback_type').val(1);
        $('#file').val($(this).data('href'));
        $('.popover_wrap').show();
    })


});
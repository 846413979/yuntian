$(document).ready(function () {
    /***** 产品分类展开收缩 *****/
    $('#product_category').change(function () {
            change();
    })


    /*** 行业案例点击 ***/
    $('.product_industry_list_item').click(function () {
        $(this).toggleClass('active')

        change();
    })



    // ajax请求列表数据
    function change() {
        let industry = [];
        $('.product_industry_list .product_industry_list_item.active').each(function () {
            industry.push($(this).data('id'))
        })

        const data = {
            profession_ids: industry.join(',')
        };

        let url = $('#product_category option:selected').data('url');

        $.ajax({
            url: url,
            type: 'get',
            data: data,
            dataType: 'json',
            success: function (res) {
                if (res.code === 0) {
                    alert(res.msg)
                }
                let list = res.data.list
                var html = '';
                for (var i = 0; i < list.length; i++) {
                        html += '<li class="product_item">' +
                            '<div class="product_list_item_img">' +
                            '<img src="' + list[i].url + '" alt="' + list[i].name + '">' +
                            '</div>' +
                            '<div class="product_list_item_title">' +
                            list[i].name +
                            '</div>' +
                            '</li>'
                }
                $('.product_list').html(html)

                $('.pagination').html(res.data.page)
            }
        })
    }


});
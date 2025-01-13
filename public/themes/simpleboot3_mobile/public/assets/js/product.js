$(document).ready(function() {
    // 搜索
    $('#product_category').change(function (){
        change()
    })
    $('#product_tonnage').change(function (){
        change()
    })
    $('.product_industry .product_industry_list .product_industry_list_item').click(function (){
        if($(this).hasClass('active')){
            $(this).removeClass('active')
        }else {
            $(this).addClass('active')
        }
        change()
    })

    function change(){
        let industry = [];
        $('.product_industry .product_industry_list .product_industry_list_item.active').each(function (){
            industry.push($(this).data('id'))
        })


        const data = {
            weight: $('#product_tonnage').val(),
            profession_ids: industry.join(',')
        };
        let url = $('#product_category').find('option:selected').data('url');
        $.ajax({
            url: url,
            type: 'get',
            data:data,
            dataType: 'json',
            success: function (res) {
                if(res.code === 0){
                    alert(res.msg)
                }
                let list = res.data.list.data
                var html = '';
                for (var i = 0; i < list.length; i++) {
                    html += '<li class="products-content-list-item" data-id="'+list[i].id+'">' +
                        '<a href="'+list[i].url+'">' +
                        '<div class="products-content-list-item-img">' +
                        '<img src="'+list[i].thumbnail+'" alt="'+list[i].title+'">' +
                        '</div>' +
                        '<div class="products-content-list-item-title">' +
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
                        '</li>'
                }
                $('.products-content-list').html(html)

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
})
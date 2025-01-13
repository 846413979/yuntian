
var width = window.innerWidth;

// 设置html的font-size为视口宽度的1/10
document.documentElement.style.fontSize = (width / 37.5) + 'px';
$(document).ready(function () {
    // 导航栏显示
    $('.header .nav .nav-btn').click(function (){
        $(this).siblings('.nav-list').toggle();
    })

    // 子导航显示
    $('.header .nav ul li .nav-sub-btn').click(function (){
        if ($(this).hasClass('active')){
            $(this).removeClass('active');
            $(this).siblings('.nav-sub').hide();
            $(this).html('+')
        }else{
            $(this).addClass('active');
            $(this).siblings('.nav-sub').show();
            $(this).html('-')
        }
    })

    $('.footer-feedback-btn').click(function (){
        let name=$('input[name=footer-name]').val();
        let email=$('input[name=footer-email]').val();
        let message=$('input[name=footer-message]').val();
        if(name==''||email==''||message=='') {
            alert('请填写完整信息')
            return;
        }
        let data = {
            name: name,
            email: email,
            content: message,
            type:2,
        }
        $.ajax({
            url: '/portal/index/inquiry',
            type: 'POST',
            data:data,
            dataType: 'json',
            success: function (res) {
                if (res.code == 1) {
                    alert('submit success');
                } else {
                    alert('submit failed:'+res.msg);
                }
            }
        })
    })


    $('.right_fix_form').click(function() {
        $('.popover_wrap').show();
    });

    $('.popover_wrap .popover_container .popover_close').click(function (){
        $('#feedback_type').val(2);
        $('#file').val('');
        $('.popover_wrap').hide();
    })

    $('#popover-submit').click(function (){
        let name = $('#popover-name').val();// 姓名
        let phone = $('#popover-phone').val();// 手机号
        let email = $('#popover-email').val();// 邮箱
        let content = $('#popover-content').val();// 内容

        if (!name || !phone || !email || !content){
            alert('Please fill in all the information completely.');
            return;
        }
        gtag('set', 'user_data', {
            "email": email,
            "phone_number": phone
        });
        let type = $('#feedback_type').val();
        let data = {
            name: name,
            phone: phone,
            email: email,
            content: content,
        };
        if (type==1){
            data.type = 1;
            product_feedback(data);
        } else if (type==3){
            data.type = 3;
            download_feedback(data);
        }else{
            data.type = 2;
            feedback(data);
        }
    })


    function product_feedback(data){
        let product_id = $('#product-id').val(); // 产品id
        let lifting_capacity = []; // 起重量
        $('.info_right_capacity_list_item.active').each(function (){
            var key = $(this).data('key');
            lifting_capacity.push(key);
        })
        let min_height = $('#minValue').text();
        let max_height = $('#maxValue').text();
        let height = min_height+','+max_height; // 起重高度
        let min_span = $('#minSpanValue').text();
        let max_span = $('#maxSpanValue').text();
        let span = min_span+','+max_span; // 跨度
        let voltage = $('.info_voltage_select').val(); // 工作电压
        let hertz = $('.info_hertz_select').val(); // 工作频率
        let job_level = []; // 工作等级
        $('.info_right_job_list_item.active').each(function (){
            var key = $(this).data('key');
            job_level.push(key);
        })
        let sling_available = []; // 吊具
        $('.info_available_list_item.active').each(function (){
            var key = $(this).data('key');
            sling_available.push(key);
        })
        data.product_id = product_id;
        data.lifting_capacity = lifting_capacity;
        data.lifting_height = height;
        data.span = span;
        data.operating_voltage = voltage;
        data.operating_herts = hertz;
        data.job_level = job_level;
        data.sling_available = sling_available;
        let file_url = $('#file').val();
        $.ajax({
            url: '/portal/index/inquiry',
            type: 'POST',
            data:data,
            dataType: 'json',
            success: function (res) {
                if (res.code == 1) {
                    alert('submit success');
                    //谷歌点击转化
                    gtag_report_conversion();
                    $('#feedback_type').val(2);
                    $('.popover_wrap').hide();
                    if(file_url!==''){
                        $('#file').val('');
                        window.location.href=file_url;
                    }
                } else {
                    alert('submit failed:'+res.msg);
                }
            }
        })
    }

    function download_feedback(data){
        let $file_url = $('#file_url');
        let file = $file_url.val();

        $.ajax({
            url: '/portal/index/inquiry',
            type: 'POST',
            data:data,
            dataType: 'json',
            success: function (res) {
                if (res.code == 1) {
                    alert('submit success');
                    $('#feedback_type').val(2);
                    $('#session').val(res.data.session)
                    $('.popover_wrap').hide();
                    $file_url.val('');
                    window.open(file)
                } else {
                    alert('submit failed:'+res.msg);
                }
            }
        })
    }

    function feedback(data){
        $.ajax({
            url: '/portal/index/inquiry',
            type: 'POST',
            data:data,
            dataType: 'json',
            success: function (res) {
                if (res.code == 1) {
                    alert('submit success');
                    $('#feedback_type').val(2);
                    $('.popover_wrap').hide();
                } else {
                    alert('submit failed:'+res.msg);
                }
            }
        })
    }

})
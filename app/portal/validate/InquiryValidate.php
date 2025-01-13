<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\portal\validate;

use app\admin\model\RouteModel;
use think\Validate;

class InquiryValidate extends Validate
{
    protected $rule = [
        'name' => 'require',
//        'phone' => 'require',
        'email' => 'require|email',
        'type' => 'require|in:1,2,3',
        'product_id' => 'requireIf:type,1',
//        'lifting_capacity' => 'requireIf:type,1',
//        'lifting_height' => 'requireIf:type,1',
//        'span' => 'requireIf:type,1',
//        'operating_voltage' => 'requireIf:type,1',
//        'operating_herts' => 'requireIf:type,1',
//        'job_level' => 'requireIf:type,1',
//        'sling_available' => 'requireIf:type,1',
    ];
    protected $message = [
        'name.require' => 'Please Enter Your Name',
        'phone.require' => 'Please Enter Your Phone or WhatsApp',
        'email.require' => 'Please Enter Your Email',
        'email.email' => 'Email Is Invalid',
        'lifting_capacity.requireIf' => 'Please Select Lifting Capacity',
        'lifting_height.requireIf' => 'Please Select Lifting Height',
        'span.requireIf' => 'Please Select Span',
        'operating_voltage.requireIf' => 'Please Select Voltage',
        'operating_herts.requireIf' => 'Please Select Herts',
        'job_level.requireIf' => 'Please Select Job Level',
        'sling_available.requireIf' => 'Please Select Sling Available',
    ];

}
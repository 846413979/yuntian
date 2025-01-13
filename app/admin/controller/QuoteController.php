<?php

namespace app\admin\controller;

use cmf\controller\AdminBaseController;

class QuoteController extends AdminBaseController
{

    // 询价设置
    public function site(){
        $quote_setting = cmf_get_option('quote_site');
        $this->assign('site_info',$quote_setting);

        return $this->fetch();
    }


    public function sitePost(){
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }
        $data = $this->request->post();
        $site_info = [];

        if(!empty($data['image'])){
            $site_info['image'] = $data['image'];
        }

        cmf_set_option('quote_site', $site_info);

        $this->success('保存成功');
    }

}
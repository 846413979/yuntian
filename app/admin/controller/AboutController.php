<?php

namespace app\admin\controller;

use cmf\controller\AdminBaseController;

class AboutController extends AdminBaseController
{

    // 关于我们设置
    public function site(){
        $about_setting = cmf_get_option('about_site');
        $this->assign('site_info',$about_setting);

        return $this->fetch();
    }


    public function sitePost(){
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }
        $data = $this->request->post();
        $site_info = [];
        if(!empty($data['top_title'])){
            $site_info['top_title'] = $data['top_title'];
        }
        if(!empty($data['top_text'])){
            $site_info['top_text'] = $data['top_text'];
        }
        if(!empty($data['top_image'])){
            $site_info['top_image'] = $data['top_image'];
        }
        if(!empty($data['middle_text'])){
            $site_info['middle_text'] = $data['middle_text'];
        }
        if(!empty($data['bottom_image'])){
            $site_info['bottom_image'] = $data['bottom_image'];
        }
        if(!empty($data['essence'])){
            $site_info['essence'] = $data['essence'];
        }
        if(!empty($data['working_methods'])){
            $site_info['working_methods'] = $data['working_methods'];
        }
        if(!empty($data['wolves_spirit'])){
            $site_info['wolves_spirit'] = $data['wolves_spirit'];
        }

        if (!empty($data['photo_urls']) && !empty($data['photo_names'])) {
            $site_info['certificate_images'] = [];
            foreach ($data['photo_urls'] as $key => $url) {
                $photoUrl = cmf_asset_relative_url($url);
                $site_info['certificate_images'][] = ["url" => $photoUrl, "name" => $data['photo_names'][$key]];
            }
        }

        cmf_set_option('about_site', $site_info);

        $this->success('保存成功');
    }

}
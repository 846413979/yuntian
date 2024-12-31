<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-present http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\RouteModel;
use app\admin\model\UserModel;
use cmf\controller\AdminBaseController;

/**
 * Class SettingController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   =>'设置',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 0,
 *     'icon'   =>'cogs',
 *     'remark' =>'系统设置入口'
 * )
 */
class SettingController extends AdminBaseController
{

    /**
     * 网站信息
     * @adminMenu(
     *     'name'   => '网站信息',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 0,
     *     'icon'   => '',
     *     'remark' => '网站信息',
     *     'param'  => ''
     * )
     */
    public function site()
    {
        $content = hook_one('admin_setting_site_view');

        if (!empty($content)) {
            return $content;
        }

        $noNeedDirs     = [".", "..", ".svn", 'fonts'];
        $adminThemesDir = WEB_ROOT . config('template.cmf_admin_theme_path') . config('template.cmf_admin_default_theme') . '/public/assets/themes/';
        $adminStyles    = cmf_scan_dir($adminThemesDir . '*', GLOB_ONLYDIR);
        $adminStyles    = array_diff($adminStyles, $noNeedDirs);
        $cdnSettings    = cmf_get_option('cdn_settings');
        $cmfSettings    = cmf_get_option('cmf_settings');
        $adminSettings  = cmf_get_option('admin_settings');
        $productSettings  = cmf_get_option('product_setting');
        $indexSettings  = cmf_get_option('index_setting');

        $adminThemes = [];
        $themes      = cmf_scan_dir(WEB_ROOT . config('template.cmf_admin_theme_path') . '/*', GLOB_ONLYDIR);

        foreach ($themes as $theme) {
            if (strpos($theme, 'admin_') === 0) {
                array_push($adminThemes, $theme);
            }
        }

        if (APP_DEBUG && false) { // TODO 没确定要不要可以设置默认应用
            $apps = cmf_scan_dir($this->app->getAppPath() . '*', GLOB_ONLYDIR);
            $apps = array_diff($apps, $noNeedDirs);
            $this->assign('apps', $apps);
        }

        $this->assign('site_info', cmf_get_option('site_info'));
        $this->assign("admin_styles", $adminStyles);
        $this->assign("templates", []);
        $this->assign("admin_themes", $adminThemes);
        $this->assign("cdn_settings", $cdnSettings);
        $this->assign("admin_settings", $adminSettings);
        $this->assign("cmf_settings", $cmfSettings);
        $this->assign("product_setting", $productSettings);
        $this->assign("index_setting", $indexSettings);

        return $this->fetch();
    }

    /**
     * 网站信息设置提交
     * @adminMenu(
     *     'name'   => '网站信息设置提交',
     *     'parent' => 'site',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '网站信息设置提交',
     *     'param'  => ''
     * )
     */
    public function sitePost()
    {
        if ($this->request->isPost()) {
            $result = $this->validate($this->request->param(), 'SettingSite');
            if ($result !== true) {
                $this->error($result);
            }

            $options = $this->request->param('options/a');
            cmf_set_option('site_info', $options);

            $cmfSettings = $this->request->param('cmf_settings/a');

            if(!empty($cmfSettings)){
                $bannedUsernames                 = preg_replace("/[^0-9A-Za-z_\\x{4e00}-\\x{9fa5}-]/u", ",", $cmfSettings['banned_usernames']);
                $cmfSettings['banned_usernames'] = $bannedUsernames;
                cmf_set_option('cmf_settings', $cmfSettings);
            }

            $cdnSettings = $this->request->param('cdn_settings/a');
            cmf_set_option('cdn_settings', $cdnSettings);

            $adminSettings = $this->request->param('admin_settings/a');

            $routeModel = new RouteModel();
            if (!empty($adminSettings['admin_password'])) {
                $routeModel->setRoute($adminSettings['admin_password'] . '$', 'admin/Index/index', [], 2, 5000);
            } else {
                $routeModel->deleteRoute('admin/Index/index', []);
            }

            $routeModel->getRoutes(true);

            if (!empty($adminSettings['admin_theme'])) {
                $result = cmf_set_dynamic_config([
                    'template' => [
                        'cmf_admin_default_theme' => $adminSettings['admin_theme']
                    ]
                ]);

                if ($result === false) {
                    $this->error('配置写入失败!');
                }
            }

            cmf_set_option('admin_settings', $adminSettings);

            $this->success(lang('EDIT_SUCCESS'), '');

        }
    }

    /**
     * 密码修改
     * @adminMenu(
     *     'name'   => '密码修改',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '密码修改',
     *     'param'  => ''
     * )
     */
    public function password()
    {
        return $this->fetch();
    }

    /**
     * 密码修改提交
     * @adminMenu(
     *     'name'   => '密码修改提交',
     *     'parent' => 'password',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '密码修改提交',
     *     'param'  => ''
     * )
     */
    public function passwordPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();
            if (empty($data['old_password'])) {
                $this->error("原始密码不能为空！");
            }
            if (empty($data['password'])) {
                $this->error("新密码不能为空！");
            }

            $userId = cmf_get_current_admin_id();

            $admin = UserModel::where("id", $userId)->find();

            $oldPassword = $data['old_password'];
            $password    = $data['password'];
            $rePassword  = $data['re_password'];

            if (cmf_compare_password($oldPassword, $admin['user_pass'])) {
                if ($password == $rePassword) {

                    if (cmf_compare_password($password, $admin['user_pass'])) {
                        $this->error("新密码不能和原始密码相同！");
                    } else {
                        UserModel::where('id', $userId)->update(['user_pass' => cmf_password($password)]);
                        $this->success("密码修改成功！");
                    }
                } else {
                    $this->error("密码输入不一致！");
                }

            } else {
                $this->error("原始密码不正确！");
            }
        }
    }

    /**
     * 上传限制设置界面
     * @adminMenu(
     *     'name'   => '上传设置',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '上传设置',
     *     'param'  => ''
     * )
     */
    public function upload()
    {
        $uploadSetting = cmf_get_upload_setting();
        $this->assign('upload_setting', $uploadSetting);
        return $this->fetch();
    }

    /**
     * 上传限制设置界面提交
     * @adminMenu(
     *     'name'   => '上传设置提交',
     *     'parent' => 'upload',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '上传设置提交',
     *     'param'  => ''
     * )
     */
    public function uploadPost()
    {
        if ($this->request->isPost()) {
            //TODO 非空验证
            $uploadSetting = $this->request->post();

            cmf_set_option('upload_setting', $uploadSetting);
            $this->success(lang('EDIT_SUCCESS'));
        }

    }

    /**
     * 清除缓存
     * @adminMenu(
     *     'name'   => '清除缓存',
     *     'parent' => 'default',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '清除缓存',
     *     'param'  => ''
     * )
     */
    public function clearCache()
    {
        $content = hook_one('admin_setting_clear_cache_view');

        if (!empty($content)) {
            return $content;
        }

        cmf_clear_cache();
        return $this->fetch();
    }


    /**
     *
     * 产品配置
     *
     * **/
    public function productSitePost()
    {

        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }

        $params = $this->request->post();

        $product_site = [];
        //认证证书
        if (!empty($params['authentication_mark_urls']) && !empty($params['authentication_mark_names'])) {
            foreach ($params['authentication_mark_urls'] as $k => $url) {
                if(empty($params['authentication_mark_names'][$k])){
                    $this->error('认证名称不能为空');
                }
                $product_site['authentication_mark'][] = [
                    'url' => $url,
                    'name' => $params['authentication_mark_names'][$k]
                ];
            }
        }
        //可配吊具
        if (!empty($params['sling_available_urls']) && !empty($params['sling_available_names'])) {
            foreach ($params['sling_available_urls'] as $k => $url) {
                if(empty($params['sling_available_names'][$k])){
                    $this->error('吊具名称不能为空');
                }
                $product_site['sling_available'][] = [
                    'url' => $url,
                    'name' => $params['sling_available_names'][$k]
                ];
            }
        }
        //可配吊具（已选中）
        if (!empty($params['sling_available_active_urls']) && !empty($params['sling_available_active_names'])) {
            foreach ($params['sling_available_active_urls'] as $k => $url) {
                if(empty($params['sling_available_active_names'][$k])){
                    $this->error('吊具名称不能为空');
                }
                $product_site['sling_available_active'][] = [
                    'url' => $url,
                    'name' => $params['sling_available_active_names'][$k]
                ];
            }
        }
        //起重量
        if(!empty($params['lifting_capacity'])){
            $product_site['lifting_capacity'] = $params['lifting_capacity'];
        }
        //工作电压
        if(!empty($params['operating_voltage'])){
            $product_site['operating_voltage'] = $params['operating_voltage'];
        }
        //工作赫兹
        if(!empty($params['operating_hertz'])){
            $product_site['operating_hertz'] = $params['operating_hertz'];
        }
        //工作等级
        if(!empty($params['job_level'])){
            $product_site['job_level'] = $params['job_level'];
        }
        //产品附件
        if (!empty($params['file_url']) && !empty($params['file_name'])) {
            $product_site['product_file'] = [
                'url' => cmf_asset_relative_url($params['file_url']),
                'name' => $params['file_name']
            ];
        }

        cmf_set_option('product_setting',$product_site);

        //清空缓存
        cmf_clear_cache();
        return $this->success('保存成功');
    }

    public function indexSitePost()
    {
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }

        $params = $this->request->post();

        $index_site = [];

        if (!empty($params['products_description'])){
            $index_site['products_description'] = $params['products_description'];
        }
        // 关于我们设置
        if (!empty($params['about_description'])){
            $index_site['about_description'] = $params['about_description'];
        }
        if (!empty($params['engineering_name']) && !empty($params['engineering_desc']) && !empty($params['engineering_num'])) {
            foreach ($params['engineering_name'] as $k => $v) {
                if(empty($params['engineering_num'][$k])){
                    $this->error('数量不能为空');
                }
                $index_site['engineering'][] = [
                    'engineering_name' => $v,
                    'engineering_desc' => $params['engineering_desc'][$k] ?? '',
                    'engineering_num' => $params['engineering_num'][$k] ?? 0,
                ];
            }
        }
        if (!empty($params['about_img'])){
            $index_site['about_img'] = $params['about_img'];
        }
        // 选择我们设置
        if (!empty($params['choose_description'])){
            $index_site['choose_description'] = $params['choose_description'];
        }
        if (!empty($params['choose_name']) && !empty($params['choose_desc']) && !empty($params['choose_image']) && !empty($params['choose_image_active'])) {
            foreach ($params['choose_name'] as $k => $v) {
                if(empty($params['choose_image'][$k])){
                    $this->error('图片不能为空');
                }
                if(empty($params['choose_image_active'][$k])){
                    $this->error('图片不能为空');
                }
                $index_site['choose'][] = [
                    'choose_name' => $v,
                    'choose_desc' => $params['choose_desc'][$k] ?? '',
                    'choose_image' => $params['choose_image'][$k] ?? '',
                    'choose_image_active' => $params['choose_image_active'][$k] ?? '',
                ];
            }
        }


        cmf_set_option('index_setting',$index_site);

        //清空缓存
        cmf_clear_cache();
        return $this->success('保存成功');

    }


}

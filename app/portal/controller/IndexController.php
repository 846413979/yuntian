<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use app\admin\model\MessageModel;
use app\admin\model\SlideItemModel;
use app\admin\model\UserAccessLogModel;
use app\portal\model\PortalPostModel;
use app\portal\model\ProductCategoryModel;
use app\portal\model\ProductModel;
use app\portal\model\ProfessionModel;
use app\portal\service\RedisService;
use app\portal\validate\InquiryValidate;
use cmf\controller\HomeBaseController;
use think\facade\Db;

class IndexController extends HomeBaseController
{

    private $isMobile;

    public function initialize()
    {
        // 产品分类
        $productCategoryModel = new ProductCategoryModel();
        $category_list = $productCategoryModel->with('children')->where('parent_id',0)->order('list_order asc,create_time desc')->select();
        $this->assign('category_list', $category_list);

        $site_info = cmf_get_site_info();
        $this->assign('site_info', $site_info);
        parent::initialize();


        // 保存访问记录到redis
        $ip = cmf_client_ip();
        $url = $this->request->url();

        $redisService = new RedisService();
        $param = $this->request->param();
        $redisService::setUrl($ip,$url,$param);

        $this->isMobile = cmf_is_mobile();

    }

    public function index()
    {

        // 首页轮播图
        $slide_id = 1;
        $SlideItemModel = new SlideItemModel();
        $slides = $SlideItemModel->field('title,image,content')->where('slide_id', $slide_id)->order('list_order asc')->select();
        $this->assign('slides', $slides);


        // 首页推荐产品
        $productModel = new ProductModel();
        $hot_products = $productModel->with('category')->where('is_recommended', 1)->order('list_order asc')->limit(8)->select();
        $this->assign('hot_products', $hot_products);

        // 首页设置
        $index_site = cmf_get_option('index_setting');
        $this->assign('index_site', $index_site);

        // 产品设置
        $product_setting = cmf_get_option('product_setting');
        $this->assign('product_setting', $product_setting);
        return $this->fetch(':index');
    }


    public function product()
    {

        $category_id = $this->request->param('id', 0, 'intval');
        $keywords = $this->request->param('keywords', '', 'strval');
        $profession_ids = $this->request->param('profession_ids', '', 'strval');//行业标签
        $productCategoryModel = new ProductCategoryModel();
        $where = [];
        if (!empty($category_id)) {
            // 判断是否有子分类
            $children = $productCategoryModel->where('parent_id', $category_id)->column('id');
            $children[] = $category_id;
            if (!empty($children)) {
                $where[] = ['category_id', 'IN', $children];
            }else{
                $where[] = ['category_id', '=', $category_id];
            }
        }
        if (!empty($profession_ids)) {
            $where[] = ['f.tag_id', 'IN', $profession_ids];
        }
        if (!empty($keywords)) {
            $where[] = ['title|alias_name', 'like', '%' . $keywords . '%'];
        }
        $productModel = new ProductModel();
        $page = 12;


        // 产品设置
        $product_setting = cmf_get_option('product_setting');
        $this->assign('product_setting', $product_setting);

        if ($this->request->isAjax()) {
            $list = $productModel->with('category')->alias('p')->join('cmf_product_profession f', 'p.id=f.product_id', 'left')->field('p.id,category_id,title,thumbnail,file')->where($where)->order('list_order asc,p.create_time desc')->group('p.id')->paginate(['list_rows' => $page, 'query' => $this->request->param()])->each(function ($item, $key) use ($product_setting){
                $item['thumbnail'] = cmf_get_image_url($item['thumbnail']);
                $item['url'] = cmf_url('portal/index/product_info', array('id' => $item['id']));
                if(!empty($item['file']) || !empty($product_setting['product_file'])){
                    $item->file = ['url'=>!empty($item['file']) ? cmf_get_file_download_url($item['file']['url']) : cmf_get_file_download_url($product_setting['product_file']['url'])];
                }
            });

            $this->success('success', '', ['list' => $list, 'page' => $list->render()]);
        }


        // 产品父级分类id
        $parent_id = $productCategoryModel->where('id', $category_id)->value('parent_id');
        if(empty($parent_id)){
            $parent_id = $category_id;
        }
        $this->assign('parent_id', $parent_id);

        $list = $productModel->with('category')->alias('p')->join('cmf_product_profession f', 'p.id=f.product_id', 'left')->field('p.id,category_id,title,thumbnail,file')->where($where)->order('list_order asc,p.create_time desc')->group('p.id')->paginate(['list_rows' => $page, 'query' => $this->request->param()]);

        $this->assign('list', $list);
        $this->assign('page', $list->render());

        // 行业标签
        $professionModel = new ProfessionModel();
        $profession_list = $professionModel->field('id,name')->where('status', 1)->select();
        foreach ($profession_list as $profession) {
            $profession_ids_arr = explode(',', $profession_ids);
            $profession['checked'] = false;
            if(in_array($profession['id'], $profession_ids_arr)){
                $profession['checked'] = true;
            }
        }
        $this->assign('profession_list', $profession_list);

        if(cmf_is_mobile()){
            $this->getBanner(7);
        }else{
            $this->getBanner(2);
        }

        return $this->fetch(':product');
    }


    public function product_info()
    {
        $productModel = new ProductModel();
        $id = $this->request->param('id', 0, 'intval');
        if (empty($id)) {
            $this->error('product not exits');
        }
        $product = $productModel->where('id', $id)->find();

        if (empty($product)) {
            $this->error('product not exits');
        }

        $this->assign('product', $product);

        $productCategoryModel = new ProductCategoryModel();
        $category = $productCategoryModel->where('id', $product['category_id'])->find();
        $this->assign('category', $category);

        // 产品设置
        $product_setting = cmf_get_option('product_setting');
        $this->assign('product_setting', $product_setting);

        // 推荐产品
        $recommended_list = $productModel->with('category')->field('id,category_id,title,thumbnail')->where([['id', '<>', $id], ['is_recommended', '=', 1]])->orderRaw('RAND()')->limit(4)->select();
        $this->assign('recommended_list', $recommended_list);

        if(cmf_is_mobile()){
            $this->getBanner(7);
        }else{
            $this->getBanner(2);
        }

        return $this->fetch(':product-info');
    }

    public function getBanner($slide_id)
    {
        // 产品banner
        $SlideItemModel = new SlideItemModel();
        $banner = $SlideItemModel->field('title,description,image')->where('slide_id', $slide_id)->order('list_order asc')->find();

        $this->assign('banner', $banner);
    }


    // 行业案例
    public function industries()
    {
        $category_id = $this->request->param('category_id', 0, 'intval');
        $keywords = $this->request->param('keywords', '', 'strval');
        $profession_ids = $this->request->param('profession_ids', '', 'strval');//行业标签
        $productCategoryModel = new ProductCategoryModel();
        $where = [];
        if (!empty($category_id)) {
            // 判断是否有子分类
            $children = $productCategoryModel->where('parent_id', $category_id)->column('id');
            $children[] = $category_id;
            if (!empty($children)) {
                $where[] = ['category_id', 'IN', $children];
            }else{
                $where[] = ['category_id', '=', $category_id];
            }
        }
        if (!empty($profession_ids)) {
            $where[] = ['f.tag_id', 'IN', $profession_ids];
        }
        if (!empty($keywords)) {
            $where[] = ['title|alias_name', 'like', '%' . $keywords . '%'];
        }
        $productModel = new ProductModel();
        $page = 12;

        if ($this->request->isAjax()) {
            $list = $productModel->alias('p')->join('cmf_product_profession f', 'p.id=f.product_id', 'left')->field('case')->where($where)->order('list_order asc,p.create_time desc')->group('p.id')->paginate(['list_rows' => $page, 'query' => $this->request->param()]);
            $items = $list->items();
            $cases = array();
            foreach ($items as $key => $item) {
                if (!empty($item['case']) && is_array($item['case'])) {
                    foreach ($item['case'] as $k => $case) {
                        $cases[$k]['url'] = cmf_get_image_url($case['url']);
                        $cases[$k]['name'] = $case['name'];
                    }
                }
            }
            $this->success('success', '', ['list' => $cases, 'page' => $list->render()]);
        }



        // 产品父级分类id
        $parent_id = $productCategoryModel->where('id', $category_id)->value('parent_id');
        if(empty($parent_id)){
            $parent_id = $category_id;
        }
        $this->assign('parent_id', $parent_id);

        $list = $productModel->alias('p')->join('cmf_product_profession f', 'p.id=f.product_id', 'left')->field('case')->where($where)->order('list_order asc,p.create_time desc')->group('p.id')->paginate(['list_rows' => $page, 'query' => $this->request->param()]);

        $this->assign('list', $list);
        $this->assign('page', $list->render());

        // 行业标签
        $professionModel = new ProfessionModel();
        $profession_list = $professionModel->field('id,name')->where('status', 1)->select();
        foreach ($profession_list as $profession) {
            $profession_ids_arr = explode(',', $profession_ids);
            $profession['checked'] = false;
            if(in_array($profession['id'], $profession_ids_arr)){
                $profession['checked'] = true;
            }
        }
        $this->assign('profession_list', $profession_list);

        if(cmf_is_mobile()){
            $this->getBanner(8);
        }else{
            $this->getBanner(3);
        }

        return $this->fetch(':industries');
    }


    // 关于我们
    public function about()
    {
        if(cmf_is_mobile()){
            $this->getBanner(9);
        }else{
            $this->getBanner(4);
        }

        $about_site = cmf_get_option('about_site');
        $this->assign('about_site', $about_site);

        $index_site = cmf_get_option('index_setting');
        $this->assign('index_site', $index_site);

        return $this->fetch(':about');
    }


    //  服务页面
    public function service(){
        if(cmf_is_mobile()){
            $this->getBanner(11);
        }else{
            $this->getBanner(6);
        }
        $portalPostModel = new PortalPostModel();
        $content = $portalPostModel->where('id',1)->value('post_content');
        $this->assign('content', $content);
        return $this->fetch(':service');
    }


    // 联系我们
    public function quote()
    {
        if(cmf_is_mobile()){
            $this->getBanner(10);
        }else{
            $this->getBanner(5);
        }
        $site_info = cmf_get_option('site_info');
        $this->assign('site_info', $site_info);

        $quote_site = cmf_get_option('quote_site');
        $this->assign('quote_site', $quote_site);

        return $this->fetch(':quote');
    }


    // 提交表单
    public function inquiry()
    {
        if (!$this->request->isPost()) {
            $this->error('Invalid request');
        }
        $data = $this->request->param();
        $InquiryValidate = new InquiryValidate();
        if (!$InquiryValidate->check($data)) {
            $this->error($InquiryValidate->getError());
        }
        Db::startTrans();
        try {
            // 保存消息
            $messageModel = new MessageModel();
            $ip = cmf_client_ip();
            $messageModel->saveMessage($ip,$data);
            $message_id = $messageModel->id;

            $UserAccessLogModel = new UserAccessLogModel();
            $UserAccessLogModel->saveLog($ip,$message_id);

            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }

        if ($data['type'] == 3) {
            session('user_download', 1);
        }
        $this->success('submit success', '', ['session' => session('user_download')]);
    }

}

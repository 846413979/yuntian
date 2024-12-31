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

use app\admin\model\SlideItemModel;
use app\portal\model\ProductCategoryModel;
use app\portal\model\ProductModel;
use app\portal\model\ProfessionModel;
use app\portal\service\RedisService;
use cmf\controller\HomeBaseController;

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
        $hot_products = $productModel->with('category')->where('is_recommended', 1)->order('list_order asc')->limit(20)->select();
        $this->assign('hot_products', $hot_products);

        // 首页设置
        $index_site = cmf_get_option('index_setting');
        $this->assign('index_site', $index_site);
        return $this->fetch(':index');
    }


    public function product()
    {

        $category_id = $this->request->param('id', 0, 'intval');
        $keywords = $this->request->param('keywords', '', 'strval');
        $profession_ids = $this->request->param('profession_ids', '', 'strval');//行业标签
        $where = [];
        if (!empty($category_id)) {
            // 判断是否有子分类
            $productCategoryModel = new ProductCategoryModel();
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
            $list = $productModel->with('category')->alias('p')->join('cmf_product_profession f', 'p.id=f.product_id', 'left')->field('p.id,category_id,title,thumbnail')->where($where)->order('list_order asc,p.create_time desc')->group('p.id')->paginate(['list_rows' => $page, 'query' => $this->request->param()])->each(function ($item, $key) {
                $item['thumbnail'] = cmf_get_image_url($item['thumbnail']);
                $item['url'] = cmf_url('portal/index/product_info', array('id' => $item['id']));
            });

            $this->success('success', '', ['list' => $list, 'page' => $list->render()]);
        }



        // 产品父级分类id
        $parent_id = $productCategoryModel->where('id', $category_id)->value('parent_id');
        if(empty($parent_id)){
            $parent_id = $category_id;
        }
        $this->assign('parent_id', $parent_id);

        $list = $productModel->with('category')->alias('p')->join('cmf_product_profession f', 'p.id=f.product_id', 'left')->field('p.id,category_id,title,thumbnail')->where($where)->order('list_order asc,p.create_time desc')->group('p.id')->paginate(['list_rows' => $page, 'query' => $this->request->param()]);

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

        $this->getBanner(2);

        // 产品设置
        $product_setting = cmf_get_option('product_setting');
        $this->assign('product_setting', $product_setting);


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
        $recommended_list = $productModel->field('id,title,thumbnail')->where([['id', '<>', $id], ['is_recommended', '=', 1]])->orderRaw('RAND()')->limit(12)->select();
        $this->assign('recommended_list', $recommended_list);

        $this->getBanner(2);

        return $this->fetch(':product-info');
    }

    public function getBanner($slide_id)
    {
        // 产品banner
        $SlideItemModel = new SlideItemModel();
        $banner = $SlideItemModel->field('title,description,image')->where('slide_id', $slide_id)->order('list_order asc')->find();

        $this->assign('banner', $banner);
    }

}

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
namespace app\portal\controller;

use app\admin\model\RouteModel;
use app\portal\model\PortalCategoryPostModel;
use app\portal\model\PortalTagPostModel;
use app\portal\model\ProductCategoryModel;
use app\portal\model\ProductModel;
use app\portal\model\RecycleBinModel;
use cmf\controller\AdminBaseController;
use app\portal\model\PortalPostModel;
use app\portal\service\PostService;
use app\portal\model\PortalCategoryModel;
use app\admin\model\ThemeModel;
use think\Db;

class AdminProductController extends AdminBaseController
{
    public function index()
    {

        $param = $this->request->param();
        $categoryId = $this->request->param('category', 0, 'intval');

        $where = [];
        if (!empty($categoryId)) {
            $where[] = ['category_id', '=', $categoryId];
        }
        if (!empty($param['start_time']) && !empty($param['end_time'])) {
            $where[] = ['create_time', 'between', [strtotime($param['start_time']), strtotime($param['end_time'])]];
        }else{
            if (!empty($param['start_time'])) {
                $where[] = ['create_time', '>=', strtotime($param['start_time'])];
            }
            if (!empty($param['end_time'])) {
                $where[] = ['create_time', '<=', strtotime($param['end_time'])];
            }
        }

        if (!empty($param['keyword'])) {
            $where[] = ['title', 'like', '%' . $param['keyword'] . '%'];
        }

        $productModel = new ProductModel();
        $data = $productModel->with('category')->where($where)->order('list_order ASC, id DESC')->paginate(10);

        $data->appends($param);

        $productCategoryModel = new ProductCategoryModel();
        $categoryList = $productCategoryModel->select();

        $this->assign('start_time', $param['start_time'] ?? '');
        $this->assign('end_time', $param['end_time'] ?? '');
        $this->assign('keyword', $param['keyword'] ?? '');
        $this->assign('products', $data->items());
        $this->assign('category_list', $categoryList);
        $this->assign('category', $categoryId);
        $this->assign('page', $data->render());


        return $this->fetch();
    }

    public function add()
    {
        $categoryModel = new ProductCategoryModel();
        $categoryList = $categoryModel->productCategoryTree();
        $this->assign('category_list', $categoryList);

        $product_setting = cmf_get_option('product_setting');
        $this->assign($product_setting);

        return $this->fetch();
    }

    public function select()
    {
        $key = $this->request->param('key');

        $product_setting = cmf_get_option('product_setting');
        if (empty($product_setting[$key])) {
            $this->error('参数错误');
        }
        $this->assign('list', $product_setting[$key]);

        $ids = $this->request->param('ids');
        $selectedIds = explode(',', $ids);
//        var_dump($selectedIds);exit();
        $this->assign('selectedIds', $selectedIds);

        return $this->fetch();
    }

    public function addPost()
    {
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }
        $data = $this->request->post();

        $productCategoryModel = new ProductCategoryModel();
        $category = $productCategoryModel->find($data['category_id']);
        if (empty($category)) {
            $this->error('分类不存在');
        }

        $result = $this->validate($data, 'Product');
        if ($result !== true) {
            $this->error($result);
        }

        if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
            $data['photos'] = [];
            foreach ($data['photo_urls'] as $key => $url) {
                $photoUrl = cmf_asset_relative_url($url);
                array_push($data['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
            }
        }


        if (!empty($data['certificate_names']) && !empty($data['certificate_urls'])) {
            $data['certificate'] = [];
            foreach ($data['certificate_urls'] as $key => $url) {
                $photoUrl = cmf_asset_relative_url($url);
                array_push($data['certificate'], ["url" => $photoUrl, "name" => $data['certificate_names'][$key]]);
            }
        }

        if (!empty($data['file_name']) && !empty($data['file_url'])) {
            $data['file'] = [
                'url' => cmf_asset_relative_url($data['file_url']),
                'name' => $data['file_name']
            ];
        }
        $product_setting = cmf_get_option('product_setting');

        if(!empty($data['authentication_mark'])){
            $authentication_mark = $data['authentication_mark'];
            $data['authentication_mark'] = [];
            foreach ($authentication_mark as $key => $value){
                $data['authentication_mark'][] = ['url'=>$product_setting['authentication_mark'][$value]['url']??'','name'=>$product_setting['authentication_mark'][$value]['name']??''];
            }
        }

        if(!empty($data['job_level']) && !$this->checkEmpty($data['job_level'])){
            $this->error('工作等级不能为空值');
        }

        $data['input_field'] = implode(',',$data['input_field']);


        $model = new ProductModel();
        $model->startTrans();
        $result = $model->save($data);
        $id  = $model->id;

        $professionRes = true;
        if (!empty($data['tags'])){
            $professionRes = $model->profession()->saveAll(explode(",", $data['tags']));
        }


        if ($result === false || $professionRes === false) {
            $model->rollback();
            $this->error('添加失败!');
        }
        $model->commit();
        //设置别名
        $routeModel = new RouteModel();
        if (!empty($data['alias'])) {
            $routeModel->setRoute($data['alias'], 'portal/index/product_info', ['id' => $id], 2, 2);
            $routeModel->getRoutes(true);
        }

        $this->success('添加成功!', url('AdminProduct/index'));

    }

    // 判断数组中是否存在空值
    public function checkEmpty($data){
        foreach ($data as $key => $value){
            if(empty($value)){
                return false;
            }
        }
        return true;
    }

    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        $productModel = new ProductModel();
        $product = $productModel->find($id);
        if (empty($product)) {
            $this->error('产品不存在!');
        }

        $categoryModel = new ProductCategoryModel();
        $categoryList = $categoryModel->productCategoryTree($product['category_id']);
        $this->assign('category_list', $categoryList);

        $product_setting = cmf_get_option('product_setting');
        $this->assign($product_setting);

        $productTags  = $product->profession()->alias('a')->column('a.name', 'a.id');
        $productTagIds = implode(',', array_keys($productTags));

        $this->assign('product', $product);
        $this->assign('productTags', $productTags);
        $this->assign('productTagIds', $productTagIds);

        return $this->fetch();
    }


    public function editPost()
    {

        if (!$this->request->isPost()) {
            $this->error('请求方式出错!');
        }
        $data = $this->request->post();

        $productCategoryModel = new ProductCategoryModel();
        $category = $productCategoryModel->find($data['category_id']);
        if (empty($category)) {
            $this->error('分类不存在');
        }

        $result = $this->validate($data, 'Product');
        if ($result !== true) {
            $this->error($result);
        }

        if (empty($data['thumbnail'])) {
            $this->error('缩略图不能为空');
        }

        if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
            $data['photos'] = [];
            foreach ($data['photo_urls'] as $key => $url) {
                $photoUrl = cmf_asset_relative_url($url);
                array_push($data['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
            }
        }

        if (!empty($data['certificate_names']) && !empty($data['certificate_urls'])) {
            $data['certificate'] = [];
            foreach ($data['certificate_urls'] as $key => $url) {
                $photoUrl = cmf_asset_relative_url($url);
                array_push($data['certificate'], ["url" => $photoUrl, "name" => $data['certificate_names'][$key]]);
            }
        }

        if (!empty($data['file_name']) && !empty($data['file_url'])) {
            $data['file'] = [
                'url' => cmf_asset_relative_url($data['file_url']),
                'name' => $data['file_name']
            ];
        }
//        var_dump($data['files']);exit();

        $product_setting = cmf_get_option('product_setting');

        if(!empty($data['authentication_mark'])){
            $authentication_mark = $data['authentication_mark'];
            $data['authentication_mark'] = [];
            foreach ($authentication_mark as $key => $value){
                $data['authentication_mark'][] = ['url'=>$product_setting['authentication_mark'][$value]['url']??'','name'=>$product_setting['authentication_mark'][$value]['name']??''];
            }
        }

        if(!empty($data['job_level']) && !$this->checkEmpty($data['job_level'])){
            $this->error('工作等级不能为空值');
        }
        $data['input_field'] = implode(',',$data['input_field']);

//        var_dump($data);exit;

        $model = new ProductModel();
        $model->startTrans();
        $product = $model->find($data['id']);
        $result = $product->save($data);
        if (!empty($data['tags'])) {
            $tags = explode(',', $data['tags']);
        }else{
            $tags = [];
        }

        $oldTagIds        = $product->profession()->column('tag_id');
        $sameTagIds       = array_intersect($tags, $oldTagIds);
        $needDeleteTagIds = array_diff($oldTagIds, $sameTagIds);
        $newTagIds        = array_diff($tags, $sameTagIds);

        if (!empty($needDeleteTagIds)) {
            $product->profession()->detach($needDeleteTagIds);
        }

        if (!empty($newTagIds)) {
            $product->profession()->attach(array_values($newTagIds));
        }

        if ($result === false) {
            $model->rollback();
            $this->error('保存失败!');
        }
        $model->commit();

        //设置别名
        $routeModel = new RouteModel();
        if (!empty($data['alias'])) {
            $routeModel->setRoute($data['alias'], 'portal/index/product_info', ['id' => $data['id']], 2, 2);
            $routeModel->getRoutes(true);
        }

        $this->success('保存成功!');

    }

    public function delete()
    {
        $param = $this->request->param();
        $model = new ProductModel();

        if (isset($param['id'])) {
            $id = $this->request->param('id', 0, 'intval');
            $result = $model->destroy($id);
            if ($result === false) {
                $this->error('删除失败！');
            }
            $this->success("删除成功！", '');
        }

        if (isset($param['ids'])) {
            $ids = $this->request->param('ids/a');
            $result = $model->destroy($ids);
            if ($result === false) {
                $this->error('删除失败！');
            }
            $this->success("删除成功！");
        }
    }

    /**
     * 文章推荐
     * @adminMenu(
     *     'name'   => '文章推荐',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章推荐',
     *     'param'  => ''
     * )
     */
    public function recommend()
    {
        $param = $this->request->param();
        $model = new ProductModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $model->where('id', 'in', $ids)->update(['is_recommended' => 1]);

            $this->success("推荐成功！", '');

        }
        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $model->where('id', 'in', $ids)->update(['is_recommended' => 0]);

            $this->success("取消推荐成功！", '');

        }

        if (isset($param['id']) && isset($param["yes"])) {
            $id = $this->request->param('id/d');

            $model->where('id', $id)->update(['is_recommended' => 1]);

            $this->success("推荐成功！", '');

        }
        if (isset($param['id']) && isset($param["no"])) {
            $id = $this->request->param('id/d');

            $model->where('id', 'in', $id)->update(['is_recommended' => 0]);

            $this->success("取消推荐成功！", '');

        }
    }

    public function listOrder()
    {
        parent::listOrders('product');
        $this->success("排序更新成功！", '');
    }
}

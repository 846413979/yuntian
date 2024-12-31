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
use app\portal\model\ProductCategoryModel;
use cmf\controller\AdminBaseController;


class AdminProductCategoryController extends AdminBaseController
{

    public function index()
    {
        $productCategoryModel = new ProductCategoryModel();
        $list = $productCategoryModel->productCategoryTableTree();
        $this->assign('list', $list);

        return $this->fetch();
    }

    public function add()
    {

        $categoryModel = new ProductCategoryModel();
        $parent_id = $this->request->param('parent', 0, 'intval');
        $this->assign('parent_id', $parent_id);

        $oneCategory = $categoryModel->where('parent_id', 0)->select();
        $this->assign('oneCategory', $oneCategory);

        return $this->fetch();
    }

    public function addPost()
    {
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }

        $data = $this->request->post();

        $result = $this->validate($data, 'ProductCategory');

        if ($result !== true) {
            $this->error($result);
        }
        if (empty($data['list_order'])){
            unset($data['list_order']);
        }

        $categoryModel = new ProductCategoryModel();
        if(!empty($data['parent_id'])){
            $parent = $categoryModel->where('id', $data['parent_id'])->find();
            if(empty($parent)){
                $this->error('父级分类不存在');
            }
            if($parent['parent_id'] != 0){
                $this->error('父级分类不能是子级分类');
            }
        }

        $result = $categoryModel->save($data);

        if ($result === false) {
            $this->error('添加失败!');
        }
        $id = $categoryModel->id;

        //设置别名
        $routeModel = new RouteModel();
        if (!empty($data['alias']) && !empty($id)) {
            $routeModel->setRoute($data['alias'], 'portal/index/product', ['id' => $id], 2, 1);
            $routeModel->getRoutes(true);
        }


        $this->success('添加成功!', url('AdminProductCategory/index'));
    }

    public function edit()
    {

        $id = $this->request->param('id', 0, 'intval');

        $categoryModel = new ProductCategoryModel();

        $oneCategory = $categoryModel->where('parent_id', 0)->select();
        $this->assign('oneCategory', $oneCategory);

        $category = $categoryModel->find($id)->toArray();
        $this->assign($category);

        return $this->fetch();

    }

    public function editPost()
    {
        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }

        $data = $this->request->post();
//        var_dump($data);exit();
        $result = $this->validate($data, 'ProductCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $categoryModel = new ProductCategoryModel();
        if(!empty($data['parent_id'])){
            $parent = $categoryModel->where('id', $data['parent_id'])->find();
            if(empty($parent)){
                $this->error('父级分类不存在');
            }
            if($parent['parent_id'] != 0){
                $this->error('父级分类不能是子级分类');
            }
            if($parent['id'] == $data['id']){
                $this->error('不能选择自己作为父级分类');
            }
        }
        $category = $categoryModel->find($data['id']);
        $result = $category->save($data);

        if ($result === false) {
            $this->error('保存失败!');
        }
        //设置别名
        $routeModel = new RouteModel();
        if (!empty($data['alias'])) {
            $routeModel->setRoute($data['alias'], 'portal/index/product', ['id' => $data['id']], 2, 1);
            $routeModel->getRoutes(true);
        }

        $this->success('保存成功!',url('AdminProductCategory/index'));
    }

    public function listOrder()
    {
        parent::listOrders('product_category');
        $this->success("排序更新成功！", '');
    }

    public function delete()
    {
        $categoryModel = new ProductCategoryModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findCategory = $categoryModel->where('id', $id)->find();

        if (empty($findCategory)) {
            $this->error('分类不存在!');
        }
        $categoryPostCount = $categoryModel->getCategoryProductCount($id);

        if ($categoryPostCount > 0) {
            $this->error('此分类有产品无法删除!');
        }

        $result = $categoryModel->destroy($id);
        if ($result) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
}

<?php

namespace app\portal\controller;

use app\portal\model\ProfessionModel;
use cmf\controller\AdminBaseController;

class AdminProfessionController extends AdminBaseController
{

    private $model;

    protected function initialize()
    {
        $this->model = new ProfessionModel();
        parent::initialize();
    }
    public function index(){
        $list = $this->model->select();
        $this->assign("arrStatus", $this->model::$STATUS);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add(){
        $this->assign("arrStatus", $this->model::$STATUS);
        return $this->fetch();
    }

    public function addPost(){
        if (!$this->request->isPost()){
            $this->error("请求错误");
        }
        $data = $this->request->post();
        $result = $this->model->save($data);
        if($result){
            $this->success("添加成功");
        }else{
            $this->error("添加失败");
        }
    }

    public function edit(){
        $id = $this->request->param('id');
        $info = $this->model->find($id);
        $this->assign('info',$info);
        $this->assign("arrStatus", $this->model::$STATUS);
        return $this->fetch();
    }
    public function editPost(){
        if (!$this->request->isPost()){
            $this->error("请求错误");
        }
        $data = $this->request->post();
        $result = $this->model->save($data,['id'=>$data['id']]);
        if($result){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }

    public function delete(){
        $id = $this->request->param('id');
        $result = $this->model->destroy($id);
        if($result){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

    public function upStatus(){
        $id = $this->request->param('id');
        $status = $this->request->param('status');
        $result = $this->model->exists()->save(['status'=>$status,'id'=>$id]);
        if($result){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }

    public function select()
    {
        $ids                 = $this->request->param('ids');
        $selectedIds         = explode(',', $ids);
        $list = $this->model->where('status',1)->select();
        $this->assign('list',$list);
        $this->assign('selectedIds',$selectedIds);
        return $this->fetch();
    }
}
<?php

namespace app\admin\controller;

use app\admin\model\MessageModel;
use app\admin\model\UserAccessLogModel;
use cmf\controller\AdminBaseController;

class MessageController extends AdminBaseController
{
    public function index(){
        $where = array();
        $start_time = $this->request->param('start_time');
        $end_time = $this->request->param('end_time');
        if (!empty($start_time) && !empty($end_time)) {
            $where[] = array('create_time','between', [strtotime($start_time), strtotime($end_time)+86400-1]);
        }else{
            if (!empty($start_time)) {
                $where[] = array('create_time','>=', strtotime($start_time));
            }
            if (!empty($end_time)) {
                $where[] = array('create_time','<=', strtotime($end_time)+86400-1);
            }
        }
        $MessageModel = new MessageModel();
        $data = $MessageModel->adminMessageList($where);

        $this->assign('list', $data->items());
        $this->assign('page', $data->render());

        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);

        return $this->fetch();
    }

    public function delete()
    {
        $param           = $this->request->param();
        $type = $this->request->param('type',0);//删除类型：true为彻底删除，false为逻辑删除
        $deleteType = (bool)$type;

        $messageModel = new MessageModel();
        if (isset($param['id'])) {
            $result = $messageModel->destroy($param['id'],$deleteType);
        }else{
            $result = $messageModel->destroy($param['ids'],$deleteType);
        }
        if ($result) {
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
    }

    // 已删除列表
    public function deletedList(){
        $where = array();
        $start_time = $this->request->param('start_time');
        $end_time = $this->request->param('end_time');
        if (!empty($start_time) && !empty($end_time)) {
            $where[] = array('create_time','between', [strtotime($start_time), strtotime($end_time)+86400-1]);
        }else{
            if (!empty($start_time)) {
                $where[] = array('create_time','>=', strtotime($start_time));
            }
            if (!empty($end_time)) {
                $where[] = array('create_time','<=', strtotime($end_time)+86400-1);
            }
        }
        $MessageModel = new MessageModel();
        $data = $MessageModel->deletedMessageList($where);

        $this->assign('list', $data->items());
        $this->assign('page', $data->render());

        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);

        return $this->fetch();
    }


    // 恢复
    public function restore()
    {
        $param           = $this->request->param();
        $messageModel = new MessageModel();
        if (isset($param['id'])) {
            $result = $messageModel->restore([['id',$param['id']]]);
        }else{
            $result = $messageModel->restore([['id','in',$param['ids']]]);
        }
        if ($result) {
            $this->success("恢复成功！");
        }else{
            $this->error("恢复失败！");
        }
    }


    //查看详情
    public function info()
    {
        $id = $this->request->param('id',0,'intval');
        $MessageModel = new MessageModel();
        $data = $MessageModel->find($id);
        $this->assign('data', $data);
        return $this->fetch();
    }

    // 用户访问列表
    public function user_access_log(){
        $message_id = $this->request->param('id',0,'intval');
        $UserAccessLogModel = new UserAccessLogModel();
        $list = $UserAccessLogModel->where('message_id',$message_id)->order('id asc')->paginate(['list_rows'=>10,'query'=>['id'=>$message_id]]);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        return $this->fetch();
    }
}
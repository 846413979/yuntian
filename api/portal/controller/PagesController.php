<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\portal\controller;

use api\portal\service\PortalPostService;
use cmf\controller\RestBaseController;
use api\portal\model\PortalPostModel;

class PagesController extends RestBaseController
{
    /**
     * 页面列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @OA\Get(
     *     tags={"portal"},
     *     path="/portal/pages",
     *     @OA\Response(response=200,ref="#/components/responses/200")
     * )
     */
    public function index()
    {
        $params      = $this->request->get();
        $postService = new PortalPostService();
        $data        = $postService->postArticles($params, true);

        if (empty($this->apiVersion) || $this->apiVersion == '1.0.0') {
            $response = $data;
        } else {
            $response = ['list' => $data,];
        }
        $this->success('请求成功!', $response);
    }

    /**
     * 获取页面
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @OA\Get(
     *     tags={"portal"},
     *     path="/portal/pages/{id}",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="页面id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(response=200,ref="#/components/responses/200")
     * )
     */
    public function read($id)
    {
        $params    = $this->request->param();
//        $field     = empty($params['field']) ? '*' : $params['field'];
        $postModel = new PortalPostModel();
        $data      = $postModel
//            ->field($field)
            ->where('id', $id)
            ->where('delete_time', 0)
            ->where('post_status', 1)
            ->where('post_type', 2)
            ->find();
        if ($data){
            $this->success('请求成功!', $data);
        }else{
            $this->error('页面不存在！');
        }

    }
}

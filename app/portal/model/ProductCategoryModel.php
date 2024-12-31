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
namespace app\portal\model;

use think\db\Query;
use think\Model;
use tree\Tree;

class ProductCategoryModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'product_category';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';

    public function getCategoryProductCount($id)
    {
        $total = ProductModel::where('category_id', $id)->count();
        return $total;
    }

    // 定义关联方法
    public function children()
    {
        return $this->hasMany(ProductCategoryModel::class, 'parent_id', 'id');
    }

    // 获取分类列表
    public function getListByParent(){
        $list = $this->where('parent_id', 0)->select();
        $tree = new Tree();
        foreach ($list as $k => $v) {
            $list[$k]['child'] = $this->where('parent_id', $v['id'])->select();
        }
        return $list;
    }


    public function productCategoryTree($selectId = 0)
    {
        $categories = $this->order("list_order ASC")->select()->toArray();

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        $tree->init($categories);
        $str = '<option value="$id" $selected>$spacer$name</option>';
        return $tree->getTree(0, $str, $selectId);
    }

    public function productCategoryTableTree($currentIds = 0,$tpl = '')
    {
//        if (!empty($currentCid)) {
//            $where['id'] = ['neq', $currentCid];
//        }
        $categories = $this->order("list_order ASC")->select()->toArray();

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        if (!is_array($currentIds)) {
            $currentIds = [$currentIds];
        }


        $newCategories = [];
        foreach ($categories as $item) {
            $item['parent_id_node'] = ($item['parent_id']) ? ' class="child-of-node-' . $item['parent_id'] . '"' : '';
            $item['style']          = empty($item['parent_id']) ? '' : 'display:none;';
            $item['checked']        = in_array($item['id'], $currentIds) ? "checked" : "";
            $item['str_action'] = '';
            if ($item['parent_id'] == 0) {
                $item['str_action'] = '<a class="btn btn-xs btn-primary" href="' . url("adminProductCategory/add", ["parent" => $item['id']]) . '">添加子分类</a>  ';
            }
            $item['str_action']     .= '<a class="btn btn-xs btn-primary" href="' . url("adminProductCategory/edit", ["id" => $item['id']]) . '">' . lang('EDIT') . '</a>  <a class="btn btn-xs btn-danger js-ajax-delete" href="' . url("adminProductCategory/delete", ["id" => $item['id']]) . '">' . lang('DELETE') . '</a> ';
            $item['url'] = cmf_url('portal/index/product',array('id'=>$item['id']));
            $item['thumbnail'] = cmf_get_image_url($item['thumbnail']);
            array_push($newCategories, $item);
        }


        $tree->init($newCategories);

        if (empty($tpl)) {
            $tpl = "<tr>
                        <td><input name='list_orders[\$id]' class='input-order' type='text' value='\$list_order'></td>   
                        <td>\$id</td>
                        <td><a href='\$url' target='_blank'>\$spacer \$name</a></td>
                        <td><a href='javascript:parent.imagePreviewDialog(\"\$thumbnail\");'><img src='\$thumbnail' style='width: 30px;height: 30px;'></td>
                        <td>\$str_action</td>
                    </tr>";
        }
        $treeStr = $tree->getTree(0, $tpl);
        return $treeStr;
    }



}

<?php

namespace Composer\Http\Traits;

trait Select
{

    /**
     * Select选择器
     */
    public function getSelectList()
    {
        $this->buildFilter();
        $this->list = $this->model->where('state', true)->select('id as value', 'name as label')->get();
        return $this->success($this->list);
    }

    /**
     * getCategoryList选择器
     */
    public function getCategoryList()
    {
        $this->buildFilter();
        $list = $this->model->where('state', true)->get();
        $this->list = self::_generateTree($list);
        return $this->success($this->list);
    }

    public static function _generateTree($data, $pid = 0)
    {
        $tree = [];
        foreach ($data as $v) {
            if ($v['parent_id'] == $pid) {
                if ($children = self::_generateTree($data, $v['id'])) {
                    $v['children'] = $children;
                }
                $tree[] = $v;
            }
        }
        return $tree;
    }
}

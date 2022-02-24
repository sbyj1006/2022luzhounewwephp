<?php

namespace app\admin\model;

use think\Model;


class Personage extends Model
{

    

    

    // 表名
    protected $name = 'personage';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'Type_text',
//        'client_type_name_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }

    public function getRecommendList()
    {
        return ['0' => __('Recommend 0'), '1' => __('Recommend 1')];
    }

//    public function getClient_type_nameList()
//    {
//        return ['1' => __('client_type_name 1'), '2' => __('client_type_name 2')];
//    }


    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2'), '3' => __('Type 3')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

//
//    public function getClient_type_nameTextAttr($value, $data)
//    {
//        $value = $value ? $value : (isset($data['client_type_name']) ? $data['client_type_name'] : '');
//        $list = $this->getClient_type_nameList();
//        return isset($list[$value]) ? $list[$value] : '';
//    }




    public function belonguser()
    {
        return $this->belongsTo('user', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}

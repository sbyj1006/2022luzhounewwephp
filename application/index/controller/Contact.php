<?php

namespace app\index\controller;
use think\Db;
use app\common\controller\Frontend;
use app\index\model\Article;
use think\Request;

class Contact extends Frontend
{


    public function index(){//关于我们
        $Article = new Article();
        $id=25;
        $list=db('article')->where(array('tid'=>$id,'status'=>1))->find();
        $this->assign('list', $list);
        $title=db('category')->where(array('id'=>$id))->find();
        $this->assign('id',$id);
        $this->assign('title',$title);
        $banner= DB::name('banner')->where(array('tid'=>10,'status'=>1))->order('sort asc')->select();
        $this->assign('banner',$banner);
        return $this->fetch();
    }


    public function msg(){
        if($this->request->isPost()){
            $data['name'] = input('post.name');
            $data['phone'] = input('post.phone');
            $data['remarks'] = input('post.remarks');
            $data['createtime'] = time();
            $message=db('message')->insertGetId($data);
            if($message){
                $data =1;
                return json($data);
            }else{
                $data =2;
                return json($data);
            }

        }
    }











}

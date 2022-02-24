<?php

namespace app\index\controller;
use think\Db;
use app\common\controller\Frontend;
use app\index\model\Article;
class Join extends Frontend
{


    public function index(){
        $Article = new Article();
        $id=input('id');
        if($id){
            $id=input('id');
        }else{
            $fistdata=Db::name('category')->where(array('pid'=>4))->order("weigh asc")->find();
            $id=$fistdata['id'];
        }
        $list=$Article->news_list($id);
//        dump($list);
        $ftitle=Db::name('category')->where(array('id'=>$id))->find();
        $title=Db::name('category')->where(array('id'=>$ftitle['pid']))->find();
        $types=Db::name('category')->where(array('pid'=>$ftitle['pid']))->order('rank desc')->select();//顶级

        $this->assign('types', $types);
        $this->assign('id', $id);
        $this->assign('list', $list);
        $this->assign('ftitle',$ftitle);
        $this->assign('title',$title);
        $banner= DB::name('banner')->where(array('tid'=>5,'status'=>1))->order('sort asc')->select();
        $this->assign('banner',$banner);
        return $this->fetch();
    }








}

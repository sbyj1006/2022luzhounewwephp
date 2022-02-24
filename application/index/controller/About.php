<?php

namespace app\index\controller;
use think\Db;
use app\common\controller\Frontend;
use app\index\model\Article;
class About extends Frontend
{


    public function index(){//关于我们
        $Article = new Article();
//        $id=input('id');

//        if($id){
//
//        }else{
            $about_nav=Db::name('category')->where(array('pid'=>1,'status'=>'normal'))->order('rank desc')->select();//
            $this->assign('about_nav', $about_nav);
            $id=$about_nav[0]['id'];

//        }


        $fz=Db::name('article')->where(array('tid'=>6,'status'=>1))->order('sort asc')->select();//发展历程
        $this->assign('fz',$fz);
        $hz=Db::name('article')->where(array('tid'=>30,'status'=>1))->select();//合作单位
//        $hz_img=explode(',',$hz['images']);
        $this->assign('hz_img',$hz);

        $listt=Db::name('article')->where(array('tid'=>$id,'status'=>1))->order('sort desc')->order('addtime desc')->select();//公司介绍
        $this->assign('listt', $listt);
//        dump($id);die();
        $title=Db::name('category')->where(array('id'=>$id))->find();
        $this->assign('title',$title);
        $types=Db::name('category')->where(array('pid'=>1,'status'=>'normal'))->order('rank desc')->select();//顶级
        $this->assign('types',$types);
        $banner= DB::name('banner')->where(array('tid'=>6,'status'=>1))->order('sort asc')->select();
        $this->assign('banner',$banner);

//        dump($types);
        $ftitle=Db::name('category')->where(array('id'=>$title['pid']))->find();
        $this->assign('ftitle',$ftitle);

        return $this->fetch();
    }








    }

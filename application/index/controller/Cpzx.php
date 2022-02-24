<?php

namespace app\index\controller;
use think\Db;
use app\common\controller\Frontend;
use app\index\model\Article;
class Cpzx extends Frontend
{


    public function index(){//产品中心
        $Article = new Article();
        $id=input('id');
        if($id){

        }else{
            $id=Db::name('category')->where(array('pid'=>19,'status'=>'normal'))->order('rank desc')->value('id');//
        }
//dump($id);die();
        $list=$Article->cases_list($id);
        $this->assign('list', $list);
        $banner= DB::name('banner')->where(array('tid'=>11,'status'=>1))->order('sort asc')->select();
        $this->assign('banner',$banner);

        $ftitle=Db::name('category')->where(array('id'=>$id))->find();
        $title=Db::name('category')->where(array('id'=>$ftitle['pid']))->find();
        $types=Db::name('category')->where(array('pid'=>$ftitle['pid']))->order('rank desc')->select();//顶级
        $this->assign('types', $types);
        $this->assign('id', $id);

        $this->assign('ftitle',$ftitle);
        $this->assign('title',$title);
        return $this->fetch();
    }




    public function  cpzxs_detail(){//
        $banner= DB::name('banner')->where(array('tid'=>11,'status'=>1))->order('sort asc')->select();
        $this->assign('banner',$banner);
        $id=input('id');
        if($id){}else{

        }
        $list = Db::name('article')->where(array('id'=>$id,'status'=>1))->find();
        DB::name('article')->where('id',$list['id'])->setInc('dian');
        $ids=$list['tid'];
        $ftitle=Db::name('category')->where(array('id'=>$list['tid']))->find();
//        dump($list);die();
        $title=Db::name('category')->where(array('id'=>$ftitle['pid']))->find();
        $type=Db::name('category')->where(array('id'=>$ftitle['pid']))->find();
        $types=Db::name('category')->where(array('pid'=>$ftitle['pid']))->order('rank desc')->select();//顶级
          $this->assign('list', $list);
        $this->assign('types',$types);
        $this->assign('type',$type);
        $this->assign('id',$ids);
        $this->assign('ftitle',$ftitle);
        $this->assign('title',$title);
        return $this->fetch();
    }




    }

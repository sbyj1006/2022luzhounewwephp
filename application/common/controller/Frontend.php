<?php

namespace app\common\controller;

use app\common\library\Auth;
use think\Config;
use think\Controller;
use think\Hook;
use think\Lang;
use think\Loader;
use think\Validate;
use think\Session;
use think\Db;
/**
 * 前台控制器基类
 */
class Frontend extends Controller
{



    public function _initialize()
    {
        $this->nav();//顶部导航栏
        $this->weizhi();//位置
        $this->middle();//中间部分
        $this->footer_nav();//底部导航栏
        $this->link();//友情链接
//        $this->banner();//banner信息
        $this->isMobile();
        $this->information();//网站基础信息
    }

    public  function  nav(){//导航栏
        $nav_title= Db::name('category')->where(array('pid'=>0,'status'=>'normal'))->order('rank desc')->select();
        foreach ($nav_title as $key => $value) {
            $map2['pid'] = $value['id'];
            $map2['status'] = 'normal';
            $z_nav = Db::name('category')->where($map2)->order('rank desc')->select();
            $nav_title[$key]['z_nav'] = $z_nav;
        }
        $this->assign('nav',$nav_title);
    }

    public function  weizhi(){//位置
        $id=input('id');
        $ftitle= Db::name('category')->where(array('id'=>$id,'status'=>'normal'))->find();
        $id2=$ftitle['pid'];
        $title= Db::name('category')->where(array('id'=>$id2,'status'=>'normal'))->find();
        $this->assign('ftitle',$ftitle);
        $this->assign('title',$title);

    }


    public function middle(){//中间部分
        $id=input('id');
        $ftitle=Db::name('category')->where(array('id'=>$id,'status'=>'normal'))->find();
        $id2=$ftitle['pid'];
        $type=Db::name('category')->where(array('id'=>$id2,'status'=>'normal'))->find();//顶级
        $types=Db::name('category')->where(array('pid'=>$id2,'status'=>'normal'))->order('rank desc')->select();//副级
        $this->assign('id', $id);

        $this->assign('type', $type);
        $this->assign('type', $type);
        $this->assign('types', $types);
    }

    public function footer_nav(){
        $footer_nav= Db::name('plate')->where(array('pid'=>3,'status'=>1))->order('sort asc')->select();

        $this->assign('footer_nav',$footer_nav);
    }

    public function information(){//网站基础信息
        $information = Db::name('information')->where(array('status'=>'1'))->find();
        $link = Db::name('link')->where(array('status'=>'1'))->order('rank asc')->select();//友情链接
        $bkxx = Db::name('plate')->where(array('status'=>'1','pid'=>'2'))->order('sort asc')->select();
        $this->assign('information',$information);
        $this->assign('seo_title', $information['seo_title']);
        $this->assign('jj_title', $information['jj_title']);
        $this->assign('seo_wztitle', $information['wz_title']);
        $this->assign('seo_keywords', $information['seo_keywords']);
        $this->assign('seo_description', $information['seo_description']);
        $this->assign('link', $link);
        $this->assign('bkxx', $bkxx);
    }



    public function  link(){
        $link= Db::name('link')->where(array('status'=>1))->order('rank asc')->select();
        $this->assign('link',$link);
    }

    //判断是否是手机端还是电脑端
    public function isMobile(){
        // 如果有Http_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])){
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])){
            $clientkeywords = array ('nokia',
                'sony',
                'eriCSSon',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])){
            // 如果只支持wml并且不支持HTML那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
                return true;
            }
        }
        return false;
    }


}

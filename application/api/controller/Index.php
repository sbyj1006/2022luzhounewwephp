<?php

namespace app\api\controller;
use Qcloud\Sms\SmsSingleSender;
use think\Controller;
use app\common\controller\Api;
use think\Db;
/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function indexa()
    {

        exit(json_encode(['code' => 200,
            'msg' => '首页数据获取成功',

        ]));

    }


    public function getdataa()
    {
        $id = input('id');
        if ($id) {

        } else {
            $id = 3;
        }
        $datas['topbanner'] = Db::name('banner')->where(array('tid' => 1, 'status' => 1))->order('sort asc')->select();
        $datas['indexbj'] = Db::name('banner')->where(array('tid' => 2, 'status' => 1))->order('sort desc')->find();
        $datas['gonggao'] = Db::name('banner')->where(array('tid' => 15, 'status' => 1))->order('sort desc')->find();

        $datas['topbanner_ph'] = Db::name('banner')->where(array('tid' => 12, 'status' => 1))->order('sort desc')->select();
        $datas['topbanner_news'] = Db::name('banner')->where(array('tid' => 13, 'status' => 1))->order('sort desc')->find();
        $datas['topbanner_user'] = Db::name('banner')->where(array('tid' => 14, 'status' => 1))->order('sort desc')->find();

        exit(json_encode(['code' => 200,
            'msg' => '首页数据获取成功',
            'id' => $id, 'datas' => $datas,
        ]));

    }

    public function getpmdata()
    {
        $id = input('id');

        $pmdata['allOrderNumber']=Db::name('user')->order('allOrderNumber desc')->limit(3)->select();

        foreach ($pmdata['allOrderNumber'] as $k=>$v){
            $pmdata['allOrderNumber'][$k]['dianzan']=Db::name('isgood')->where(array('aid'=>$v['id'],'types'=>1))->limit(3)->select();
        }

        $pmdata['shoudanOrderNumber']=Db::name('user')->order('shoudanOrderNumber desc')->limit(3)->select();

        foreach ($pmdata['shoudanOrderNumber'] as $kk=>$vv){
            $pmdata['shoudanOrderNumber'][$kk]['dianzan']=Db::name('isgood')->where(array('aid'=>$vv['id'],'types'=>2))->limit(3)->select();
        }

        $pmdata['daikuanOrderNumber']=Db::name('user')->order('daikuanOrderNumber desc')->limit(3)->select();

        foreach ($pmdata['daikuanOrderNumber'] as $kkk=>$vvv){
            $pmdata['daikuanOrderNumber'][$kkk]['dianzan']=Db::name('isgood')->where(array('aid'=>$vvv['id'],'types'=>3))->limit(3)->select();
        }

        exit(json_encode(['code' => 200,
            'msg' => '数据p获取成功',
            'id' => $id, 'pmdata' => $pmdata,
        ]));

    }

    public function getpmdatanew()
    {
        $id = input('id');

        $where_a['iscs']=2;

        $where_a['allOrderNumber']=array('>',0);

        $pmdata['allOrderNumber']=Db::name('user')->where($where_a)->order('allOrderNumber desc')->select();

        foreach ($pmdata['allOrderNumber'] as $k=>$v){
            $pmdata['allOrderNumber'][$k]['dianzan']=Db::name('isgood')->where(array('aid'=>$v['id'],'types'=>1))->limit(3)->select();
        }


        $where_b['iscs']=2;
        $where_b['shoudanOrderNumber']=array('>',0);

        $pmdata['shoudanOrderNumber']=Db::name('user')->where($where_b)->order('shoudanOrderNumber desc')->select();

        foreach ($pmdata['shoudanOrderNumber'] as $kk=>$vv){
            $pmdata['shoudanOrderNumber'][$kk]['dianzan']=Db::name('isgood')->where(array('aid'=>$vv['id'],'types'=>2))->limit(3)->select();
        }

        $where_c['iscs']=2;
        $where_c['daikuanOrderNumber']=array('>',0);

        $pmdata['daikuanOrderNumber']=Db::name('user')->where($where_c)->order('daikuanOrderNumber desc')->select();
        foreach ($pmdata['daikuanOrderNumber'] as $kkk=>$vvv){
            $pmdata['daikuanOrderNumber'][$kkk]['dianzan']=Db::name('isgood')->where(array('aid'=>$vvv['id'],'types'=>3))->limit(3)->select();
        }

$totalsa=0;
        $totalsb=0;
        $totalsc=0;
        foreach ($pmdata['allOrderNumber'] as $key=>$val){
            $totalsa+=$val['allOrderNumber'];
        }
        foreach ($pmdata['shoudanOrderNumber'] as $key=>$val){
            $totalsb+=$val['shoudanOrderNumber'];
        }
        foreach ($pmdata['daikuanOrderNumber'] as $key=>$val){
            $totalsc+=$val['daikuanOrderNumber'];
        }

        $pmdata['totalsa']=$totalsa;
        $pmdata['totalsb']=$totalsb;
        $pmdata['totalsc']=$totalsc;
        exit(json_encode(['code' => 200,
            'msg' => '数据p获取成功',
            'id' => $id, 'pmdata' => $pmdata,
        ]));

    }



    public function getpmdatateam()
    {
        $id = input('id');

        $where_a['isrank']=1;
$where_a['levels']=2;
        $where_a['allOrderNumber']=array('>',0);

        $pmdata['allOrderNumber']=Db::name('categoryuser')->where($where_a)->order('allOrderNumber desc')->select();
        $where_b['levels']=2;
        $where_b['isrank']=1;
        $where_b['shoudanOrderNumber']=array('>',0);

        $pmdata['shoudanOrderNumber']=Db::name('categoryuser')->where($where_b)->order('shoudanOrderNumber desc')->select();
        $where_c['isrank']=1;
        $where_c['daikuanOrderNumber']=array('>',0);
        $where_c['levels']=2;
        $pmdata['daikuanOrderNumber']=Db::name('categoryuser')->where($where_c)->order('daikuanOrderNumber desc')->select();

        $totalsa=0;
        $totalsb=0;
        $totalsc=0;
        foreach ($pmdata['allOrderNumber'] as $key=>$val){
            $totalsa+=$val['allOrderNumber'];
        }
        foreach ($pmdata['shoudanOrderNumber'] as $key=>$val){
            $totalsb+=$val['shoudanOrderNumber'];
        }
        foreach ($pmdata['daikuanOrderNumber'] as $key=>$val){
            $totalsc+=$val['daikuanOrderNumber'];
        }

        $pmdata['totalsa']=$totalsa;
        $pmdata['totalsb']=$totalsb;
        $pmdata['totalsc']=$totalsc;
        exit(json_encode(['code' => 200,
            'msg' => '数据p获取成功',
            'id' => $id, 'pmdata' => $pmdata,
        ]));

    }


    public function getpmdatazhihang()
    {
        $id = input('id');

        $where_a['isrank']=1;
        $where_a['levels']=1;
        $where_a['allOrderNumber']=array('>',0);

        $pmdata['allOrderNumber']=Db::name('categoryuser')->where($where_a)->order('allOrderNumber desc')->select();
        $where_b['levels']=1;
        $where_b['isrank']=1;
        $where_b['shoudanOrderNumber']=array('>',0);

        $pmdata['shoudanOrderNumber']=Db::name('categoryuser')->where($where_b)->order('shoudanOrderNumber desc')->select();
        $where_c['isrank']=1;
        $where_c['daikuanOrderNumber']=array('>',0);
        $where_c['levels']=1;
        $pmdata['daikuanOrderNumber']=Db::name('categoryuser')->where($where_c)->order('daikuanOrderNumber desc')->select();

        $totalsa=0;
        $totalsb=0;
        $totalsc=0;
        foreach ($pmdata['allOrderNumber'] as $key=>$val){
            $totalsa+=$val['allOrderNumber'];
        }
        foreach ($pmdata['shoudanOrderNumber'] as $key=>$val){
            $totalsb+=$val['shoudanOrderNumber'];
        }
        foreach ($pmdata['daikuanOrderNumber'] as $key=>$val){
            $totalsc+=$val['daikuanOrderNumber'];
        }

        $pmdata['totalsa']=$totalsa;
        $pmdata['totalsb']=$totalsb;
        $pmdata['totalsc']=$totalsc;
        exit(json_encode(['code' => 200,
            'msg' => '数据p获取成功',
            'id' => $id, 'pmdata' => $pmdata,
        ]));

    }
public function getnewsdata(){
    $id = input('id');
    $page = input('page', 1);
    $pagea=($page-1)*8;

    $newslist=Db::name('article')->where('status',1)->order('createtime desc')->limit($pagea,20)->select();

    exit(json_encode(['code' => 200,
        'msg' => '数据n获取成功',
        'id' => $id, 'newslist' => $newslist,'page'=>$page,
    ]));
}


    public function getnewsxx()
    {

        $id=input('id');
        $newsxx=Db::name('article')->where('id',$id)->order('createtime desc')->find();

        $newsxx['content'] = str_replace('<img src="', '<img src="http://newyh.cdjklm.com', $newsxx['content']);
        $newsxx['dianzan']=Db::name('isgood')->where(array('aid'=>$newsxx['id'],'types'=>4))->select();
        $newsxx['dianzannum']=Db::name('isgood')->where(array('aid'=>$newsxx['id'],'types'=>4))->count();
        $newsxx['liuyanlist']=Db::name('message')->where(array('aid'=>$newsxx['id'],'types'=>4,'status'=>1))->select();


        exit(json_encode(['code' => 200,
            'msg' => '数据n获取成功',
            'id' => $id, 'newsxx' => $newsxx,
        ]));


    }

    public function checkargood(){

$aid=input('bzuid');
$uid=input('uid');
        $types=input('types');

        if($types){

        }else{

            $code=550;
            $msg='无点赞类型';
            $redata=2;

            exit(json_encode(['code' => $code,
                'msg' => $msg,
                'id' => $aid, 'redata' => $redata,
            ]));
        }

//        dump($uid);die();

        if($aid){

if($uid){}else{
    $uid=1;
}
$where['aid']=$aid;
$where['uid']=$uid;



$dzdata=Db::name('isgood')->where($where)->find();
if($dzdata){
    $code=300;
    $msg='您已经点赞过';
    $redata='1';
}else{
    $code=200;
    $msg='您觉得很赞';

    $redata['aid']=$aid;
    $redata['uid']=$uid;
    $redata['createtime']=time();
    $redata['avatarUrl']='';
    $redata['types']=$types;
}
        }else{
            $code=500;
            $msg='参数错误';
            $redata=2;
        }

        exit(json_encode(['code' => $code,
            'msg' => $msg,
            'id' => $aid, 'redata' => $redata,
        ]));
}



    public function paihangdianzana(){

        $aid=input('id');

        $types=input('types');
$openid=input('openid');

$userd=Db::name('user')->where('openid',$openid)->find();
if($userd){
        if($aid){

            $where['aid']=$aid;
            $where['uid']=$userd['id'];
            $where['types']=$types;


            $dzdata=Db::name('isgood')->where($where)->find();
            if($dzdata){
                $code=300;
                $msg='您已经点赞过';
                $redata='1';
            }else{

                if($types==4){
                    $redata['atitle']=Db::name('article')->where('id',$aid)->value('titles');


                    $redata['nickname']=Db::name('user')->where('id',$userd['id'])->value('nickname');
                    $redata['aid']=$aid;
                    $redata['uid']=$userd['id'];
                    $redata['createtime']=time();
                    $redata['avatarUrl']=$userd['avatar'];
                    $redata['types']=$types;

                    $re=Db::name('isgood')->insert($redata);


                }else{
                    $redata['atitle']=Db::name('user')->where('id',$aid)->value('nickname');

                    $redata['nickname']=Db::name('user')->where('id',$userd['id'])->value('nickname');
                    $redata['aid']=$aid;
                    $redata['uid']=$userd['id'];
                    $redata['createtime']=time();
                    $redata['avatarUrl']=$userd['avatar'];
                    $redata['types']=$types;

                    $re=Db::name('isgood')->insert($redata);

                    if($re) {
                        $usere=Db::name('user')->where('id',$aid)->find();
                        if ($usere['mobile']) {
                            // 短信应用 SDK AppID
                            $appid = 1400605895; // SDK AppID 以1400开头
                            // 短信应用 SDK AppKey
                            $appkey = "87570dfb5e76ba732f3ba42d79c69aa8";
                            // 需要发送短信的手机号码
                            $phoneNumbers = $usere['mobile'];
                            // 短信模板 ID，需要在短信控制台中申请
                            $templateId = 1224952;  // NOTE: 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
                            $smsSign = "小泸公享"; // NOTE: 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请

                            try {
                                $ssender = new SmsSingleSender($appid, $appkey);
//            $params = [rand(1000, 9999)];//生成随机数
                                $params[0] = $userd['bumenjg'] . $userd['nickname'];
                                $result = $ssender->sendWithParam("86", $phoneNumbers, $templateId, $params, $smsSign, "", "");
//        $rsp = json_decode($result);
//        return json(["result"=>$rsp->result,"code"=>$params,'res'=>$rsp]);
                            } catch (\Exception $e) {
//        echo var_dump($e);
                            }
                        }
                    }
                }
             ;
if($re){


    $code=200;
    $msg=$userd['bumenjg'].$userd['nickname'].'觉得很赞';
}else{
    $code=300;
    $msg='点赞失败';
}



            }
        }else{
            $code=500;
            $msg='参数错误';
            $redata=2;
        }
}else{

    $code=550;
    $msg='暂无人员信息';
    $redata=2;
}
        exit(json_encode(['code' => $code,
            'msg' => $msg,
            'id' => $aid, 'redata' => $redata,
        ]));
    }



    public function liuyans(){

        $aid=input('id');
$mescontent=input('mescontent');
        $types=input('types');
        $openid=input('openid');

        $userd=Db::name('user')->where('openid',$openid)->find();
        if($userd){
            if($aid){

                $where['aid']=$aid;
                $where['uid']=$userd['id'];
                $where['types']=$types;

                if($types==4){
                    $title=Db::name('article')->where('id',$aid)->value('title');
                    $titles=Db::name('article')->where('id',$aid)->value('titles');
                    $redata['atitle']=$title.'-'.$titles;
                }else{
                    $redata['atitle']=Db::name('user')->where('id',$aid)->value('nickname');
                }

                $redata['status']=1;
                    $redata['aid']=$aid;
                    $redata['uid']=$userd['id'];
                $redata['nickname']=$userd['nickname'];
                    $redata['createtime']=time();
                    $redata['avatarUrl']=$userd['avatar'];
                    $redata['types']=$types;
                    $redata['mescontent']=$mescontent;

                    $re=Db::name('message')->insert($redata);
                    if($re){
                        $code=200;
                        $msg=$userd['bumenjg'].$userd['nickname'].'留言成功';
                    }else{
                        $code=300;
                        $msg='留言成功失败';
                    }



            }else{
                $code=500;
                $msg='参数错误';
                $redata=2;
            }
        }else{

            $code=550;
            $msg='暂无人员信息';
            $redata=2;
        }
        exit(json_encode(['code' => $code,
            'msg' => $msg,
            'id' => $aid, 'redata' => $redata,'aid'=>$aid
        ]));
    }




}
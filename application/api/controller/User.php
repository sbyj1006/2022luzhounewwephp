<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use fast\Random;
use Qcloud\Sms\SmsSingleSender;
use think\Validate;

use think\Db;
/**
 * 会员接口
 */
class User extends Common
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }


    //获取openid
    public function getOpenid(){
        $appid = input('appid','wxe2c6c3ed32400877');
        $secret = input('secret','4afd2c8d56a521517d505079c1092ccd');
        $js_code = input('js_code','');

        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $data = array(
            'appid' => $appid,
            'secret' => $secret,
            'js_code' => $js_code,
            'grant_type' => 'authorization_code',
        );

        $res = httpRequest($url, 'POST', $data);
        //输出测试，正式使用请删除下面一行


        $obj = json_decode($res); //返回数组或对象
        if(isset($obj->openid)){
            if($obj->openid != null && $obj->openid != ''){
                exit(json_encode(['code'=>200, 'msg'=>'openid获取成功', 'result'=>$obj->openid]));
            }else{
                exit(json_encode(['code'=>400, 'msg'=>'openid获取失败','obj'=>$obj]));
            }
        }else{
            exit(json_encode(['code'=>420, 'msg'=>'openid获取失败','obj'=>$obj,'datas'=>$data]));
        }
    }

    public function getUser()
    {

        if($this->checkOpenid()){

            $openid = input('openid','');
            // 第三季修正
            $data['nickname'] = input('nickname','');
            $data['avatar'] = input('avatar','');
            //检索用户表
            $user = Db::name('user')->where('openid', $openid)->find();
            if($user){
                // 第三季修正
                // 当用户昵称或头像为空，同时接收的昵称或头像不为空，说明首次登录授权，需要更新用户表昵称和头像
                if($data['nickname']!=''){
                    // 更新用户表
                    $data2['wxnickname'] = $data['nickname'];
                    $data2['avatar'] = $data['avatar'];
                    Db::name('user')->where('openid', $openid)->update($data2);
                    $user= Db::name('user')->where('openid', $openid)->find();
                }

//                if($user['group_id']){
//                    $user['teamname']= Db::name('user_group')->where('id', $user['group_id'])->value('name');
//                }

                // 重置token
                $user['token'] = $this->resetToken();
                if($user['token']){
                    exit(json_encode(['code'=>200, 'msg'=>'验证成功', 'data'=>$user]));
                }else{
                    exit(json_encode(['code'=>401, 'msg'=>'token重置失败，请重新授权']));
                }
            }else{
//                $data['openid'] = input('openid','');
//                $data['nickname'] = input('nickname','');
//                $data['avatar'] = input('avatar','');
//                $data['jointime']=time();
//                $data['createtime']=time();
//                $data['token'] = getRandChar(32);

//                $data['time_out'] = time();
//                $data['group_id'] =99;
//                $id = Db::name('user')->insertGetId($data);
//                if($id) {
//                    exit(json_encode(['code' => 200, 'msg' => '授权成功','data'=>$data]));
//                }else{
//                    exit(json_encode(['code' => 400, 'msg' => '授权失败']));
//                }
                exit(json_encode(['code' => 340, 'msg' => '请绑定资料']));
            }
        }else{

            exit(json_encode(['code'=>403, 'msg'=>'登录失败，请重新授权']));
        }
    }
//
    public function getcdata(){



        $where['levels']=2;
        $where['status']=1;
        $teamdatas=Db::name('categoryuser')->where($where)->order('weigh desc')->select();

        foreach ($teamdatas as $key=>$val){
            $newteam[$key]=$val['name'];
        }

        $whereb['levels']=1;
        $whereb['status']=1;
        $zhihangdatas=Db::name('categoryuser')->where($whereb)->order('weigh desc')->select();

        foreach ($zhihangdatas as $key=>$val){
            $newzhihang[$key]=$val['name'];
        }

        $data['teamdata']=$newteam;
        $data['zhihangdatas']=$newzhihang;
        exit(json_encode(['code'=>200, 'msg'=>'团队支行获取成功','data'=>$data]));

    }
//
public function getcdatapid(){

        $zhihangid = input('zhihangid','');

        if($zhihangid){
            $where['pid']=$zhihangid;
        }

    $where['levels']=2;
    $where['status']=1;
        $teamdatas=Db::name('categoryuser')->where($where)->order('weigh desc')->select();

        foreach ($teamdatas as $key=>$val){
            $newteam[$key]=$val['name'];
        }

    $whereb['levels']=1;
    $whereb['status']=1;
    $zhihangdatas=Db::name('categoryuser')->where($whereb)->order('weigh desc')->select();

    foreach ($zhihangdatas as $key=>$val){
        $newzhihang[$key]=$val['name'];
    }

    $data['teamdata']=$newteam;
    $data['zhihangdatas']=$newzhihang;
    exit(json_encode(['code'=>200, 'msg'=>'团队支行获取成功','data'=>$data]));

}

    //用户注册添加
    public function registerfirst(){
        $data['openid'] = input('openid','');
        // 第三季修正
//        $data['nickname'] = input('nickname','');
//        $data['avatar'] = input('avatar','');
$data['avatar']=input('userInfo_avatarUrl');
        $data['wxnickname']=input('userInfo_nickname');
  $data['jobnumber'] = input('jobnumber','');
  $data['mobile'] = input('mobile','');
  $data['zhihangname'] = input('zhihangname','');
  $data['iscs'] = input('iscs','');
  $data['isziyuan'] = input('isziyuan','0');

        $data['jointime']=time();
        $data['createtime']=time();
        $data['token'] = getRandChar(32);
        $data['group_id'] = 99;
        $data['status']=1;

        if($data['zhihangname']){
            $wherezh['name']=$data['zhihangname'];
            $wherezh['levels']=1;
            $zhihang=Db::name('categoryuser')->where($wherezh)->find();
            $updatauser['zhihangid']=$zhihang['id'];
        }
//        if($data['teamname']){
//            $wherezh['name']=$data['teamname'];
//            $wherezh['levels']=2;
//            $team=Db::name('categoryuser')->where($wherezh)->find();
//            $updatauser['teamid']=$team['id'];
//        }
//$where['nickname']= $data['nickname'];
        $where['jobnumber']= 'G'.$data['jobnumber'];
        $user=Db::name('user')->where($where)->find();
        if($user){
            $updatauser['jobnumber'] = 'G'.$data['jobnumber'];
            $updatauser['mobile'] = $data['mobile'];
            $updatauser['zhihangname'] = $data['zhihangname'];
            $updatauser['iscs'] = $data['iscs'];
            $updatauser['isziyuan'] = $data['isziyuan'];
            $updatauser['jointime'] = $data['jointime'];
            $updatauser['token'] = $data['token'];
            $updatauser['status'] = $data['status'];
            $updatauser['openid'] = $data['openid'];
            $updatauser['avatar']=$data['avatar'];
            $updatauser['wxnickname']=$data['wxnickname'];


            if(!$user['openid']){
//                $whereup['nickname']= $data['nickname'];
                $whereup['jobnumber']= 'G'.$data['jobnumber'];
                $userup=Db::name('user')->where($whereup)->update($updatauser);
if($userup){
    $newsuser=Db::name('user')->where($whereup)->find();
    exit(json_encode(['code'=>200, 'msg'=>$user['bumenjg'].$user['nickname'].'欢迎登录','newsuser'=>$newsuser,'data'=>$updatauser]));
}else{
    exit(json_encode(['code'=>300, 'msg'=>'绑定失败','data'=>$updatauser]));
}

            }elseif($user['openid']==$data['openid']){

                exit(json_encode(['code'=>220, 'msg'=>'已绑定了','data'=>$updatauser]));
            }else{

                exit(json_encode(['code'=>230, 'msg'=>'其他人已绑定成功','data'=>$updatauser]));
            }


        }else{
            exit(json_encode(['code'=>400, 'msg'=>'暂无相关人员','data'=>$updatauser]));
        }


//        $id = Db::name('user')->insertGetId($data);
//        if($id){
//            $user = Db::name('user')->where('id', $id)->find();
//            exit(json_encode(['code'=>200, 'msg'=>'注册成功', 'data'=>$user]));
//        }else{
//            exit(json_encode(['code'=>400, 'msg'=>'注册失败']));
//        }
    }
    //用户资料绑定
    public function updatauser(){
        $data['openid'] = input('openid','');
        // 第三季修正
        $data['nickname'] = input('nickname','');

//        $data['avatar']=input('userInfo_avatarUrl');
        $data['wxnickname']=input('userInfo_nickname');
        $data['jobnumber'] = input('jobnumber','');
        $data['mobile'] = input('mobile','');
        $data['zhihangname'] = input('zhihangname','');

        if(input('teamname')){
            $data['teamname'] = input('teamname');
        }
        $data['iscs'] = input('iscs','');
        $data['isziyuan'] = input('isziyuan','0');

        $data['jointime']=time();
        $data['createtime']=time();
        $data['token'] = getRandChar(32);
        $data['group_id'] = 99;
        $data['status']=1;

        if($data['zhihangname']){
            $wherezh['name']=$data['zhihangname'];
            $wherezh['levels']=1;
            $zhihang=Db::name('categoryuser')->where($wherezh)->find();
            $updatauser['zhihangid']=$zhihang['id'];
        }
        if($data['teamname']){
            $wherezh['name']=$data['teamname'];
            $wherezh['levels']=2;
            $team=Db::name('categoryuser')->where($wherezh)->find();
            $updatauser['teamid']=$team['id'];
        }
        $where['nickname']= $data['nickname'];
        $where['jobnumber']= $data['jobnumber'];
        $where['openid']= $data['openid'];
        $user=Db::name('user')->where($where)->find();
        if($user){
            $updatauser['jobnumber'] = $data['jobnumber'];
            $updatauser['mobile'] = $data['mobile'];
            $updatauser['zhihangname'] = $data['zhihangname'];
            $updatauser['teamname'] = $data['teamname'];
            $updatauser['iscs'] = $data['iscs'];
            $updatauser['isziyuan'] = $data['isziyuan'];
            $updatauser['jointime'] = $data['jointime'];
            $updatauser['token'] = $data['token'];
            $updatauser['status'] = $data['status'];
            $updatauser['openid'] = $data['openid'];

            $updatauser['wxnickname']=$data['wxnickname'];


            if($user['openid']){
                $whereup['nickname']= $data['nickname'];
                $whereup['jobnumber']= $data['jobnumber'];
                $whereup['openid']= $data['openid'];
                $userup=Db::name('user')->where($whereup)->update($updatauser);
                if($userup){
                    $usernew=Db::name('user')->where($whereup)->find();
                    exit(json_encode(['code'=>200, 'msg'=>'修改成功','data'=>$usernew]));
                }else{
                    exit(json_encode(['code'=>300, 'msg'=>'修改失败','data'=>$updatauser]));
                }

            }else{

                exit(json_encode(['code'=>230, 'msg'=>'还未绑定成功','data'=>$updatauser]));
            }


        }else{
            exit(json_encode(['code'=>400, 'msg'=>'暂无相关人员','data'=>$updatauser]));
        }




    }
//用户竞赛报名
    public function updatajssq(){
        $data['openid'] = input('openid','');
        // 第三季修正
        $data['nickname'] = input('nickname','');

        $data['jobnumber'] = input('jobnumber','');
        $data['zhihangname'] = input('zhihangname','');

            $data['teamname'] = input('teamname');

        $data['iscs'] = input('iscs','1');
        $data['isziyuan'] = input('isziyuan','1');
        $data['iszhjs'] = input('iszhjs','1');
        $data['issdjs'] = input('issdjs','1');
        $data['isdkjs'] = input('isdkjs','1');



        if($data['teamname']){
            $wherezh['name']=$data['teamname'];
            $wherezh['levels']=2;
            $team=Db::name('categoryuser')->where($wherezh)->find();
            $updatauser['teamid']=$team['id'];
        }
        $where['nickname']= $data['nickname'];
        $where['jobnumber']= $data['jobnumber'];
        $where['openid']= $data['openid'];
        $user=Db::name('user')->where($where)->find();
        if($user){
            $updatauser['teamname'] = $data['teamname'];
            $updatauser['iscs'] = $data['iscs'];
            $updatauser['iszhjs'] = $data['iszhjs'];
            $updatauser['issdjs'] = $data['issdjs'];
//            $updatauser['isdkjs'] = $data['isdkjs'];
            $updatauser['isziyuan'] = $data['isziyuan'];


            if($user['openid']){
                $whereup['nickname']= $data['nickname'];
                $whereup['jobnumber']= $data['jobnumber'];
                $whereup['openid']= $data['openid'];
                $userup=Db::name('user')->where($whereup)->update($updatauser);
//                dump($updatauser);die();
                if($userup){
                    $usernew=Db::name('user')->where($whereup)->find();
                    exit(json_encode(['code'=>200, 'msg'=>'修改成功','data'=>$usernew]));
                }else{
                    exit(json_encode(['code'=>300, 'msg'=>'修改失败','data'=>$updatauser]));
                }

            }else{

                exit(json_encode(['code'=>230, 'msg'=>'还未绑定成功','data'=>$updatauser]));
            }


        }else{
            exit(json_encode(['code'=>400, 'msg'=>'暂无相关人员','data'=>$updatauser]));
        }




    }


    /**
     * 新增客户
     */
    public  function addkehu_bei(){
        $data['openid'] = input('openid','');
        // 第三季修正
        $data['client_id'] = input('client_id','');
        $data['client_name'] = input('client_name','');
        $data['addtime'] = input('addtime','');
        $data['client_type_name'] = input('client_type_name','');

        $userwhere['openid']=$data['openid'];
        $userwhere['status']=array('in','0,1');
        $user=Db::name('user')->where($userwhere)->find();

        if($user){
            $updatas['client_name']=$data['client_name'];
            $updatas['client_id']='KH'.$data['client_id'];
            $updatas['addtime']=$data['addtime'];
            $updatas['client_type_name']=$data['client_type_name'];
            $updatas['createtime']=time();
            $updatas['type']=1;
            $updatas['status']=0;
            $updatas['username']=$user['nickname'];
            $updatas['teamid']=$user['teamid'];
            $updatas['zhihangid']=$user['zhihangid'];
            $updatas['teamname']=$user['teamname'];
            $updatas['zhihangname']=$user['zhihangname'];
            $updatas['user_id']=$user['id'];

            $checkdata=Db::name('personage')->where(array('client_id'=>$updatas['client_id'],'type'=>1))->find();
if($checkdata){
    exit(json_encode(['code'=>300, 'msg'=>'系统已存在该客户号，请勿重复添加','data'=>$updatas]));
}else{
    $addnew=Db::name('personage')->insert($updatas);
    if($addnew){
        $wheresh['zhihangid']=$user['zhihangid'];
        $wheresh['sh_type']=3;
        $usersh=Db::name('user')->where($wheresh)->select();
if($usersh){
    foreach ($usersh as  $key=>$val){
        if($val['mobile']){
            // 短信应用 SDK AppID
            $appid = 1400605895; // SDK AppID 以1400开头
            // 短信应用 SDK AppKey
            $appkey = "87570dfb5e76ba732f3ba42d79c69aa8";
            // 需要发送短信的手机号码
            $phoneNumbers = $val['mobile'];
            // 短信模板 ID，需要在短信控制台中申请
            $templateId = 1224984;  // NOTE: 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
            $smsSign = "小泸公享"; // NOTE: 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请

            try {
                $ssender = new SmsSingleSender($appid, $appkey);
//            $params = [rand(1000, 9999)];//生成随机数
                $params[0]=$user['bumenjg'].$user['nickname'];
                $result = $ssender->sendWithParam("86", $phoneNumbers, $templateId, $params, $smsSign, "", "");
//        $rsp = json_decode($result);
//        return json(["result"=>$rsp->result,"code"=>$params,'res'=>$rsp]);
            } catch(\Exception $e) {
//        echo var_dump($e);
            }
        }
    }
}


        exit(json_encode(['code'=>200, 'msg'=>'新增成功','data'=>$updatas]));

    }else{
        exit(json_encode(['code'=>300, 'msg'=>'新增失败','data'=>$updatas]));

    }
}


        }else{
            exit(json_encode(['code'=>400, 'msg'=>'无权限操作','data'=>$user]));

        }


    }



    /**
     * 新增客户
     */
    public  function addkehu(){
        $data['openid'] = input('openid','');
        // 第三季修正
        $data['client_id'] = input('client_id','');
        $data['client_name'] = input('client_name','');
        $data['addtime'] = input('addtime','');
        $data['updatetime'] = input('updatetime','');
        $data['client_type_name_b'] = input('client_type_name_b','');
//        $data['addtime'] = input('addtime','');
        $data['client_type_name'] = input('client_type_name','');

        $userwhere['openid']=$data['openid'];
        $userwhere['status']=array('in','0,1');
        $user=Db::name('user')->where($userwhere)->find();

        if($user){
            $updatas['client_name']=$data['client_name'];
            $updatas['client_id']='KH'.$data['client_id'];
            $updatas['addtime']=$data['addtime'];
            $updatas['client_type_name']=$data['client_type_name'];
            $updatas['createtime']=time();
            $updatas['type']=1;
            $updatas['status']=0;
            $updatas['username']=$user['nickname'];
            $updatas['teamid']=$user['teamid'];
            $updatas['zhihangid']=$user['zhihangid'];
            $updatas['teamname']=$user['teamname'];
            $updatas['zhihangname']=$user['zhihangname'];
            $updatas['user_id']=$user['id'];

            $checkdatx=Db::name('personage_xc')->where(array('client_id'=>$updatas['client_id']))->find();
            if($checkdatx){
                exit(json_encode(['code'=>300, 'msg'=>'该客户为我行存量商户，请重新输入客户号','data'=>$updatas]));
            }

            $checkdata=Db::name('personage')->where(array('client_id'=>$updatas['client_id'],'type'=>1))->find();
            if($checkdata){
                exit(json_encode(['code'=>300, 'msg'=>'系统已存在该客户号，请勿重复添加','data'=>$updatas]));
            }else{


                if(input('issd')==2){

                    $addnew=Db::name('personage')->insert($updatas);

                    $updatas_sd=$updatas;
                    $updatas_sd['client_type_name_b']=$data['client_type_name_b'];
                    $updatas_sd['updatetime']=$data['updatetime'];
                    $updatas_sd['type']=2;
                    $updatas_sd['addtime'] = $data['updatetime'];
                    $addnew=Db::name('personage')->insert($updatas_sd);

                }else{

                    $addnew=Db::name('personage')->insert($updatas);
                }


                if($addnew){
                    $wheresh['zhihangid']=$user['zhihangid'];
                    $wheresh['sh_type']=3;
                    $usersh=Db::name('user')->where($wheresh)->select();
                    if($usersh){
                        foreach ($usersh as  $key=>$val){
                            if($val['mobile']){
                                // 短信应用 SDK AppID
                                $appid = 1400605895; // SDK AppID 以1400开头
                                // 短信应用 SDK AppKey
                                $appkey = "87570dfb5e76ba732f3ba42d79c69aa8";
                                // 需要发送短信的手机号码
                                $phoneNumbers = $val['mobile'];
                                // 短信模板 ID，需要在短信控制台中申请
                                $templateId = 1224984;  // NOTE: 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
                                $smsSign = "小泸公享"; // NOTE: 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请

                                try {
                                    $ssender = new SmsSingleSender($appid, $appkey);
//            $params = [rand(1000, 9999)];//生成随机数
                                    $params[0]=$user['bumenjg'].$user['nickname'];
                                    $result = $ssender->sendWithParam("86", $phoneNumbers, $templateId, $params, $smsSign, "", "");
//        $rsp = json_decode($result);
//        return json(["result"=>$rsp->result,"code"=>$params,'res'=>$rsp]);
                                } catch(\Exception $e) {
//        echo var_dump($e);
                                }
                            }
                        }
                    }


                    exit(json_encode(['code'=>200, 'msg'=>'申报成功','data'=>$updatas]));

                }else{
                    exit(json_encode(['code'=>300, 'msg'=>'申报失败','data'=>$updatas]));

                }
            }


        }else{
            exit(json_encode(['code'=>400, 'msg'=>'无权限操作','data'=>$user]));

        }


    }

            /**
             * 新增客户转化
             */
            public  function zhuanhuakehu(){
                $data['client_name'] = input('client_name','');
                $data['openid'] = input('openid','');
                // 第三季修正
                $data['client_type_name'] = input('client_type_name','');

                $data['client_type_name_b'] = input('client_type_name_b','');

                $data['client_id'] = input('client_id','');
                $data['updatetime']=input('updatetime');
                $data['zhtype'] = input('zhtype','');
                $data['type']=2;
//                if($data['zhtype']=='收单客户'){
//                    $data['type']=2;
//                }elseif($data['zhtype']=='贷款客户'){
//                    $data['type']=3;
//                }else{
//                    $data['type']=1;
//                }

                $userwhere['openid']=$data['openid'];
                $userwhere['status']=array('in','0,1');
                $user=Db::name('user')->where($userwhere)->find();

                if($user){
                    $updatas['client_name']=$data['client_name'];
                    $updatas['client_type_name_b']=$data['client_type_name_b'];
                    $updatas['client_type_name']=$data['client_type_name'];
                    if($data['client_id']){
                    $updatas['client_id']='KH'.$data['client_id'];
                    }
                    $updatas['updatetime']=$data['updatetime'];
                    $updatas['addtime']=$data['updatetime'];
                    $updatas['type']=$data['type'];
                    $updatas['username']=$user['nickname'];
                    $updatas['teamid']=$user['teamid'];
                    $updatas['zhihangid']=$user['zhihangid'];
                    $updatas['teamname']=$user['teamname'];
                    $updatas['zhihangname']=$user['zhihangname'];
                    $updatas['user_id']=$user['id'];
                    $updatas['status']=0;
                    $updatas['createtime']=time();

//                    $checkdata=Db::name('personage')->where(array('client_id'=>$updatas['client_id'],'type'=>1,'status'=>1))->find();
//                    if($checkdata){

                        $checkdatacf=Db::name('personage')->where(array('client_name'=>$updatas['client_name'],'type'=>$data['type']))->find();
if($checkdatacf){
    exit(json_encode(['code'=>300, 'msg'=>'系统已存在该商户名称转换，请勿重复添加','data'=>$updatas]));
}else{


    $upkehus=Db::name('personage')->insert($updatas);
    if($upkehus){


        $wheresh['zhihangid']=$user['zhihangid'];
        $wheresh['sh_type']=3;
        $usersh=Db::name('user')->where($wheresh)->select();
        if($usersh){
            foreach ($usersh as  $key=>$val){
                if($val['mobile']){
                    // 短信应用 SDK AppID
                    $appid = 1400605895; // SDK AppID 以1400开头
                    // 短信应用 SDK AppKey
                    $appkey = "87570dfb5e76ba732f3ba42d79c69aa8";
                    // 需要发送短信的手机号码
                    $phoneNumbers = $val['mobile'];
                    // 短信模板 ID，需要在短信控制台中申请
                    $templateId = 1224984;  // NOTE: 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
                    $smsSign = "小泸公享"; // NOTE: 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请

                    try {
                        $ssender = new SmsSingleSender($appid, $appkey);
//            $params = [rand(1000, 9999)];//生成随机数
                        $params[0]=$user['bumenjg'].$user['nickname'];
                        $result = $ssender->sendWithParam("86", $phoneNumbers, $templateId, $params, $smsSign, "", "");
//        $rsp = json_decode($result);
//        return json(["result"=>$rsp->result,"code"=>$params,'res'=>$rsp]);
                    } catch(\Exception $e) {
//        echo var_dump($e);
                    }
                }
            }
        }

        exit(json_encode(['code'=>200, 'msg'=>'申报成功','data'=>$updatas]));

    }else{
        exit(json_encode(['code'=>300, 'msg'=>'申报失败','data'=>$updatas]));

    }

}

//                    }else{
//
//                        exit(json_encode(['code'=>300, 'msg'=>'系统不存在改客户号，或审核还未通过','data'=>$updatas]));
//                    }


                }else{
                    exit(json_encode(['code'=>400, 'msg'=>'无权限操作','data'=>$user]));

                }


            }

    /**
     * 获取新增客户记录
     */
    public function getkehulist(){
        $data['type'] = input('type',1);
        $data['openid'] = input('openid','');

        if($data['type']==1){
            $pagename='账户申报';
        }elseif($data['type']==2){
            $pagename='收单申报';
        }elseif($data['type']==3){
            $pagename='贷款申报';
        }else{
            $pagename='账户申报';
        }

        $where['user_id']=Db::name('user')->where('openid',$data['openid'])->value('id');
        if($where['user_id']){

//            if($data['type']==1){
//                $where['type']=array('in','1,2,3');
//            }else{
                $where['type']=$data['type'];
//            }
//            $where['status']=1;
            $listdatas=Db::name('personage')->where($where)->order('createtime desc')->select();
            foreach ($listdatas as $k=>$v){

                if($v['addtime']==NULL){
                    $v['addtime']='';
                }
                if($v['upatime']==NULL){
                    $v['upatime']='';
                }
                if($v['status']==2){
                    $listdatas[$k]['zhuangtai']='审核未通过，请修改';
                    $listdatas[$k]['zt_color']="#ff0000";
                }elseif($v['status']==1){
                    $listdatas[$k]['zhuangtai']='审核通过';
                }elseif($v['status']==0){
                    $listdatas[$k]['zhuangtai']='等待审核';
                }

                if($v['type']==1){
                    $listdatas[$k]['newaddtime']=$v['addtime'];
                    $listdatas[$k]['typename']='新增客户';
                }elseif($v['type']==2){
                    $listdatas[$k]['newaddtime']=$v['addtime'];
                    $listdatas[$k]['typename']='收单转化';
                }elseif($v['type']==3){
                    $listdatas[$k]['newaddtime']=$v['addtime'];
                    $listdatas[$k]['typename']='贷款转化';
                }

            }

            exit(json_encode(['code'=>200, 'msg'=>'获取新增客户','listdatas'=>$listdatas,'pagename'=>$pagename]));
        }else{
            exit(json_encode(['code'=>400, 'msg'=>'无权限查看','listdatas'=>$where]));
        }




    }



//收单详情信息
public function getcdatasq(){
    $data['id'] = input('id');
    $where['id']=$data['id'] ;
//    $where['status']=1;
    $listdatas=Db::name('personage')->where($where)->find();

    if($listdatas){

        if($listdatas['type']==2){
            $listdatas['typename']='收单详情';
        }else{
            $listdatas['typename']='账户详情';
        }



    exit(json_encode(['code'=>200, 'msg'=>'获取申报信息','data'=>$listdatas]));
}else{
exit(json_encode(['code'=>400, 'msg'=>'暂无数据','data'=>$where]));
}


}

//账户信息修改
    public function addkehu_up(){
        $data['id'] = input('id');
        $updatas['client_id'] = input('client_id');
        $updatas['client_name'] = input('client_name');
        $updatas['addtime'] = input('addtime');
        $updatas['client_type_name'] = input('client_type_name');

        $where['id']=$data['id'] ;
        $updatas['status']=0;
        $listdatas=Db::name('personage')->where($where)->find();

        if($listdatas){


            $updatanew=Db::name('personage')->where($where)->update($updatas);

            if($updatanew){

                exit(json_encode(['code'=>200, 'msg'=>'信息修改成功','data'=>$listdatas]));
            }else{


                exit(json_encode(['code'=>300, 'msg'=>'信息无变化','data'=>$updatas]));
            }



        }else{
            exit(json_encode(['code'=>400, 'msg'=>'暂无数据','data'=>$where]));
        }

    }

//收单信息修改
    public function zhuanhuakehu_up(){
        $data['id'] = input('id');
        $updatas['client_id'] = input('client_id');
        $updatas['client_name'] = input('client_name');
        $updatas['client_type_name_b'] = input('client_type_name_b');
        $updatas['updatetime'] = input('updatetime');
        $updatas['client_type_name'] = input('client_type_name');

        $where['id']=$data['id'] ;
        $updatas['status']=0;
        $listdatas=Db::name('personage')->where($where)->find();

        if($listdatas){


            $updatanew=Db::name('personage')->where($where)->update($updatas);

            if($updatanew){

                exit(json_encode(['code'=>200, 'msg'=>'信息修改成功','data'=>$listdatas]));
            }else{


                exit(json_encode(['code'=>300, 'msg'=>'信息无变化','data'=>$updatas]));
            }



        }else{
            exit(json_encode(['code'=>400, 'msg'=>'暂无数据','data'=>$where]));
        }

    }


    /**
     * 获取新增客户记录审核数据列表
     */
    public function getkehulistsh(){
        $data['type'] = input('type',1);
        $data['openid'] = input('openid','');

        $userd=Db::name('user')->where('openid',$data['openid'])->find();
        if($userd['id']){

            $where['status']=0;

            //审核分支行人员
//$alluid=Db::name('user')->where('zhihangid',$userd['zhihangid'])->select();
//foreach ($alluid as $key=>$val){
//    $newuid[$key]=$val['id'];
//}
//            $where['user_id']=array('in',$newuid);

//            dump($where);die();
            $listdatas=Db::name('personage')->where($where)->order('createtime desc')->select();

            $shallid=[];

            foreach ($listdatas as $k=>$v){
                $shallid[$k]=$v['id'];

                if($v['addtime']==NULL){
                    $v['addtime']='';
                }
                if($v['upatime']==NULL){
                    $v['upatime']='';
                }
                if($v['status']==1){
                    $listdatas[$k]['zhuangtai']='正常';
                }else{
                    $listdatas[$k]['zhuangtai']='审核中';
                }
                if($v['type']==1){
                    $listdatas[$k]['newaddtime']=$v['addtime'];
                }else{
                    $listdatas[$k]['newaddtime']=$v['addtime'];
                }

                if($v['type']==1){
                    $listdatas[$k]['typename']='新增客户';
                }elseif($v['type']==2){
                    $listdatas[$k]['typename']='收单转化';
                }elseif($v['type']==3){
                    $listdatas[$k]['typename']='贷款转化';
                }

            }

            exit(json_encode(['code'=>200, 'msg'=>'获取新增客户','listdatas'=>$listdatas,'shallid'=>$shallid]));
        }else{
            exit(json_encode(['code'=>400, 'msg'=>'无权限查看','listdatas'=>'']));
        }




    }


    /**
     * 审核客户申报
     */

    public function shenheikehu(){
        $data['id'] = input('id');
        $data['shtypes'] = input('shtypes');
        $data['openid'] = input('openid','');

//        dump($data);die();

        $pers=Db::name('personage')->where('id',$data['id'])->find();
        if($pers['id']){



            if($data['shtypes']==1){


                if($pers['type']==1){
                    if($pers['dian']==0) {
//新增客户
                        $puser = Db::name('user')->where('id', $pers['user_id'])->find();
                        $pteam = Db::name('categoryuser')->where('id', $puser['teamid'])->find();
                        $pzhihang = Db::name('categoryuser')->where('id', $puser['zhihangid'])->find();

                        if ($puser && $pteam && $pzhihang) {

                            $addallOrderNumbera['allOrderNumber'] = $puser['allOrderNumber'] + 1;
                            $addallOrderNumbera['all_allOrderNumber'] = $puser['all_allOrderNumber'] + 1;
                            $rea = Db::name('user')->where('id', $pers['user_id'])->update($addallOrderNumbera);

                            $addallOrderNumberb['allOrderNumber'] = $pteam['allOrderNumber'] + 1;
                            $addallOrderNumberb['all_allOrderNumber'] = $pteam['all_allOrderNumber'] + 1;
                            $reb = Db::name('categoryuser')->where('id', $puser['teamid'])->update($addallOrderNumberb);

                            $addallOrderNumberc['allOrderNumber'] = $pzhihang['allOrderNumber'] + 1;
                            $addallOrderNumberc['all_allOrderNumber'] = $pzhihang['all_allOrderNumber'] + 1;
                            $rec = Db::name('categoryuser')->where('id', $puser['zhihangid'])->update($addallOrderNumberc);
                            if ($rea && $reb && $rec) {
                                $updian['dian'] = 1;
                                $updian['status']=1;
                                $persup = Db::name('personage')->where('id',$pers['id'])->update($updian);
                                if ($persup) {

                                    $msg='审核通过';
                                } else {

                                    $msg='审核未通过';
                                }

                            } else {
                                $msg='数据绑定不全';
                            }


                        } else {
                            $msg='数据绑定不全';

                        }
                    }else{
                        $msg='已入库计数';
                    }
//
                }elseif($pers['type']==2){
//                  收单客户
                    $puser=Db::name('user')->where('id',$pers['user_id'])->find();
                    $pteam=Db::name('categoryuser')->where('id',$puser['teamid'])->find();
                    $pzhihang=Db::name('categoryuser')->where('id',$puser['zhihangid'])->find();

                    if($puser && $pteam && $pzhihang){

                        $addallOrderNumbera['shoudanOrderNumber']=$puser['shoudanOrderNumber']+1;
                        $addallOrderNumbera['shoudanOrderNumber']=$puser['all_shoudanOrderNumber']+1;
                        $rea=Db::name('user')->where('id',$pers['user_id'])->update($addallOrderNumbera);

                        $addallOrderNumberb['shoudanOrderNumber']=$pteam['shoudanOrderNumber']+1;
                        $addallOrderNumberb['all_shoudanOrderNumber']=$pteam['all_shoudanOrderNumber']+1;
                        $reb=Db::name('categoryuser')->where('id',$puser['teamid'])->update($addallOrderNumberb);

                        $addallOrderNumberc['shoudanOrderNumber']=$pzhihang['shoudanOrderNumber']+1;
                        $addallOrderNumberc['all_shoudanOrderNumber']=$pzhihang['all_shoudanOrderNumber']+1;
                        $rec=Db::name('categoryuser')->where('id',$puser['zhihangid'])->update($addallOrderNumberc);
                        if($rea && $reb && $rec){
                            $updian['dian']=1;
                            $updian['status']=1;
                            $persup=Db::name('personage')->where('id',$pers['id'])->update($updian);
                            if ($persup) {

                                $msg='审核通过';
                            } else {

                                $msg='审核未通过';
                            }

                        }else{
                            $msg='数据绑定不全';

                        }


                    }else{
                        $msg='数据绑定不全';
                    }

//
                }elseif($pers['type']==3){
//贷款客户
                    $puser=Db::name('user')->where('id',$pers['user_id'])->find();
                    $pteam=Db::name('categoryuser')->where('id',$puser['teamid'])->find();
                    $pzhihang=Db::name('categoryuser')->where('id',$puser['zhihangid'])->find();

                    if($puser && $pteam && $pzhihang){

                        $addallOrderNumbera['daikuanOrderNumber']=$puser['daikuanOrderNumber']+1;
                        $addallOrderNumbera['all_daikuanOrderNumber']=$puser['all_daikuanOrderNumber']+1;
                        $rea=Db::name('user')->where('id',$pers['user_id'])->update($addallOrderNumbera);

                        $addallOrderNumberb['daikuanOrderNumber']=$pteam['daikuanOrderNumber']+1;
                        $addallOrderNumberb['all_daikuanOrderNumber']=$pteam['all_daikuanOrderNumber']+1;
                        $reb=Db::name('categoryuser')->where('id',$puser['teamid'])->update($addallOrderNumberb);

                        $addallOrderNumberc['daikuanOrderNumber']=$pzhihang['daikuanOrderNumber']+1;
                        $addallOrderNumberc['all_daikuanOrderNumber']=$pzhihang['all_daikuanOrderNumber']+1;
                        $rec=Db::name('categoryuser')->where('id',$puser['zhihangid'])->update($addallOrderNumberc);
                        if($rea && $reb && $rec){
                            $updian['dian']=1;
                            $updian['status']=1;
                            $persup=Db::name('personage')->where('id',$pers['id'])->update($updian);
                            if ($persup) {

                                $msg='审核通过';
                            } else {

                                $msg='审核未通过';
                            }

                        }else{
                            $msg='数据绑定不全';
                            dump('数据绑定不全');
                        }


                    }else{
                        $msg='数据绑定不全';
                    }

//

                }




                $code=200;

            }elseif($data['shtypes']==2){

                $updatas['status']=2;

                $res=Db::name('personage')->where('id',$data['id'])->update($updatas);
if($res){
    $code=600;
    $msg='审核不通过';
}else{
    $code=600;
    $msg='不通过失败';
}


            }




        }else{
            $code=500;
                $msg='数据有误';
        }



        $userd['user_id']=Db::name('personage')->where('id',$data['id'])->value('user_id');
        $userd['zhihangid']=Db::name('user')->where('id',$userd['user_id'])->value('zhihangid');
        $where['status']=0;

//        $alluid=Db::name('user')->where('zhihangid',$userd['zhihangid'])->select();
//        foreach ($alluid as $key=>$val){
//            $newuid[$key]=$val['id'];
//        }
//        $where['user_id']=array('in',$newuid);
//            dump($where);die();
        $listdatas=Db::name('personage')->where($where)->select();
        foreach ($listdatas as $k=>$v){

            if($v['addtime']==NULL){
                $v['addtime']='';
            }

            if($v['status']==1){
                $listdatas[$k]['zhuangtai']='正常';
            }else{
                $listdatas[$k]['zhuangtai']='审核中';
            }

                $listdatas[$k]['newaddtime']=$v['addtime'];


            if($v['type']==1){
                $listdatas[$k]['typename']='新增客户';
            }elseif($v['type']==2){
                $listdatas[$k]['typename']='收单转化';
            }elseif($v['type']==3){
                $listdatas[$k]['typename']='贷款转化';
            }

        }

        exit(json_encode(['code'=>$code, 'msg'=>$msg,'listdatas'=>$listdatas]));

    }

    /**
     * 一键审核
     */
    public function yijiansh(){
        $shallid = input('post.shallid/a');
        $data['shtypes'] = input('shtypes');
        $data['openid'] = input('openid','');
$shid=[];
        foreach ($shallid as $k=>$v){

            $pers=Db::name('personage')->where('id',$v)->find();
            if($pers['id']){



                if($pers['type']==1){
                    if($pers['dian']==0) {
//新增客户
                        $puser = Db::name('user')->where('id', $pers['user_id'])->find();
                        $pteam = Db::name('categoryuser')->where('id', $puser['teamid'])->find();
                        $pzhihang = Db::name('categoryuser')->where('id', $puser['zhihangid'])->find();

                        if ($puser && $pteam && $pzhihang) {

                            $addallOrderNumbera['allOrderNumber'] = $puser['allOrderNumber'] + 1;
                            $rea = Db::name('user')->where('id', $pers['user_id'])->update($addallOrderNumbera);

                            $addallOrderNumberb['allOrderNumber'] = $pteam['allOrderNumber'] + 1;
                            $reb = Db::name('categoryuser')->where('id', $puser['teamid'])->update($addallOrderNumberb);

                            $addallOrderNumberc['allOrderNumber'] = $pzhihang['allOrderNumber'] + 1;
                            $rec = Db::name('categoryuser')->where('id', $puser['zhihangid'])->update($addallOrderNumberc);
                            if ($rea && $reb && $rec) {
                                $updian['dian'] = 1;
                                $updian['status']=1;
                                $persup = Db::name('personage')->where('id',$pers['id'])->update($updian);
                                if ($persup) {

                                    $msg='审核通过';
                                } else {

                                    $msg='审核未通过';
                                }

                            } else {
                                $msg='数据绑定不全';
                            }


                        } else {
                            $msg='数据绑定不全';

                        }
                    }else{
                        $msg='已入库计数';
                    }
//
                }elseif($pers['type']==2){
//                  收单客户
                    $puser=Db::name('user')->where('id',$pers['user_id'])->find();
                    $pteam=Db::name('categoryuser')->where('id',$puser['teamid'])->find();
                    $pzhihang=Db::name('categoryuser')->where('id',$puser['zhihangid'])->find();

                    if($puser && $pteam && $pzhihang){

                        $addallOrderNumbera['shoudanOrderNumber']=$puser['shoudanOrderNumber']+1;
                        $rea=Db::name('user')->where('id',$pers['user_id'])->update($addallOrderNumbera);

                        $addallOrderNumberb['shoudanOrderNumber']=$pteam['shoudanOrderNumber']+1;
                        $reb=Db::name('categoryuser')->where('id',$puser['teamid'])->update($addallOrderNumberb);

                        $addallOrderNumberc['shoudanOrderNumber']=$pzhihang['shoudanOrderNumber']+1;
                        $rec=Db::name('categoryuser')->where('id',$puser['zhihangid'])->update($addallOrderNumberc);
                        if($rea && $reb && $rec){
                            $updian['dian']=1;
                            $updian['status']=1;
                            $persup=Db::name('personage')->where('id',$pers['id'])->update($updian);
                            if ($persup) {

                                $msg='审核通过';
                            } else {

                                $msg='审核未通过';
                            }

                        }else{
                            $msg='数据绑定不全';

                        }


                    }else{
                        $msg='数据绑定不全';
                    }

//
                }elseif($pers['type']==3){
//贷款客户
                    $puser=Db::name('user')->where('id',$pers['user_id'])->find();
                    $pteam=Db::name('categoryuser')->where('id',$puser['teamid'])->find();
                    $pzhihang=Db::name('categoryuser')->where('id',$puser['zhihangid'])->find();

                    if($puser && $pteam && $pzhihang){

                        $addallOrderNumbera['daikuanOrderNumber']=$puser['daikuanOrderNumber']+1;
                        $rea=Db::name('user')->where('id',$pers['user_id'])->update($addallOrderNumbera);

                        $addallOrderNumberb['daikuanOrderNumber']=$pteam['daikuanOrderNumber']+1;
                        $reb=Db::name('categoryuser')->where('id',$puser['teamid'])->update($addallOrderNumberb);

                        $addallOrderNumberc['daikuanOrderNumber']=$pzhihang['daikuanOrderNumber']+1;
                        $rec=Db::name('categoryuser')->where('id',$puser['zhihangid'])->update($addallOrderNumberc);
                        if($rea && $reb && $rec){
                            $updian['dian']=1;
                            $updian['status']=1;
                            $persup=Db::name('personage')->where('id',$pers['id'])->update($updian);
                            if ($persup) {

                                $msg='审核通过';
                            } else {

                                $msg='审核未通过';
                            }

                        }else{
                            $msg='数据绑定不全';
                            dump('数据绑定不全');
                        }


                    }else{
                        $msg='数据绑定不全';
                    }

//

                }




                $code=200;


            }

            }

        exit(json_encode(['code'=>$code, 'msg'=>$msg,]));





    }

    /**
     * 删除新增客户记录
     */
    public function delskehu(){
        $data['id'] = input('id');
        $data['openid'] = input('openid','');

    if($data['id']){

        $deldata=Db::name('personage')->where('id',$data['id'])->find();
if($deldata){

    $geren=Db::name('user')->where('id',$deldata['user_id'])->find();
    $team=Db::name('categoryuser')->where('id',$deldata['teamid'])->where('levels',2)->find();
    $zhihang=Db::name('categoryuser')->where('id',$deldata['zhihangid'])->where('levels',1)->find();
    if($deldata['type']==1){
        $pagename='新增客户';

        if($deldata['status']==1){
//个人排行减1
            $updatea['allOrderNumber']=$geren['allOrderNumber']-1;
            $gerenup=Db::name('user')->where('id',$deldata['user_id'])->update($updatea);
//            团队更新
            $updateb['allOrderNumber']=$team['allOrderNumber']-1;
            $gerenup=Db::name('categoryuser')->where('id',$deldata['teamid'])->update($updateb);
//            支行更新
            $updatec['allOrderNumber']=$zhihang['allOrderNumber']-1;
            $gerenup=Db::name('categoryuser')->where('id',$deldata['zhihangid'])->update($updatec);
//        删除
            $del=Db::name('personage')->where('id',$data['id'])->delete();
        }else{
            $del=Db::name('personage')->where('id',$data['id'])->delete();
        }

    }elseif($deldata['type']==2){
        $pagename='收单转化';


        if($deldata['status']==1){
//个人排行减1
            $updatea['allOrderNumber']=$geren['allOrderNumber']-1;
            $updatea['shoudanOrderNumber']=$geren['shoudanOrderNumber']-1;
            $gerenup=Db::name('user')->where('id',$deldata['user_id'])->update($updatea);
//            团队更新
            $updateb['allOrderNumber']=$team['allOrderNumber']-1;
            $updateb['shoudanOrderNumber']=$team['shoudanOrderNumber']-1;
            $gerenup=Db::name('categoryuser')->where('id',$deldata['teamid'])->update($updateb);
//            支行更新
            $updatec['allOrderNumber']=$zhihang['allOrderNumber']-1;
            $updatec['shoudanOrderNumber']=$zhihang['shoudanOrderNumber']-1;
            $gerenup=Db::name('categoryuser')->where('id',$deldata['zhihangid'])->update($updatec);
//        删除
            $del=Db::name('personage')->where('id',$data['id'])->delete();
        }else{
            $updatea['allOrderNumber']=$geren['allOrderNumber']-1;
            $gerenup=Db::name('user')->where('id',$deldata['user_id'])->update($updatea);
//            团队更新
            $updateb['allOrderNumber']=$team['allOrderNumber']-1;
            $gerenup=Db::name('categoryuser')->where('id',$deldata['teamid'])->update($updateb);
//            支行更新
            $updatec['allOrderNumber']=$zhihang['allOrderNumber']-1;
            $gerenup=Db::name('categoryuser')->where('id',$deldata['zhihangid'])->update($updatec);
//        删除

            $del=Db::name('personage')->where('id',$data['id'])->delete();
        }

    }elseif($deldata['type']==3){
        $pagename='贷款转化';



        if($deldata['status']==1){
//个人排行减1
            $updatea['allOrderNumber']=$geren['allOrderNumber']-1;
            $updatea['daikuanOrderNumber']=$geren['daikuanOrderNumber']-1;
            $gerenup=Db::name('user')->where('id',$deldata['user_id'])->update($updatea);
//            团队更新
            $updateb['allOrderNumber']=$team['allOrderNumber']-1;
            $updateb['daikuanOrderNumber']=$team['daikuanOrderNumber']-1;
            $gerenup=Db::name('categoryuser')->where('id',$deldata['teamid'])->update($updateb);
//            支行更新
            $updatec['allOrderNumber']=$zhihang['allOrderNumber']-1;
            $updatec['daikuanOrderNumber']=$zhihang['daikuanOrderNumber']-1;
            $gerenup=Db::name('categoryuser')->where('id',$deldata['zhihangid'])->update($updatec);
//        删除
            $del=Db::name('personage')->where('id',$data['id'])->delete();
        }else{
            $updatea['allOrderNumber']=$geren['allOrderNumber']-1;
            $gerenup=Db::name('user')->where('id',$deldata['user_id'])->update($updatea);
//            团队更新
            $updateb['allOrderNumber']=$team['allOrderNumber']-1;
            $gerenup=Db::name('categoryuser')->where('id',$deldata['teamid'])->update($updateb);
//            支行更新
            $updatec['allOrderNumber']=$zhihang['allOrderNumber']-1;
            $gerenup=Db::name('categoryuser')->where('id',$deldata['zhihangid'])->update($updatec);
//        删除

            $del=Db::name('personage')->where('id',$data['id'])->delete();
        }

    }else{
        $pagename='新增客户';
    }





if($del) {

    $where['user_id']=Db::name('user')->where('id',$deldata['user_id'])->value('id');
    if($where['user_id']){

        $where['type']=$deldata['type'];
//            $where['status']=1;
        $listdatas=Db::name('personage')->where($where)->select();
        foreach ($listdatas as $k=>$v){

            if($v['addtime']==NULL){
                $v['addtime']='';
            }
            if($v['upatime']==NULL){
                $v['upatime']='';
            }
            if($v['status']=1){
                $listdatas[$k]['zhuangtai']='正常';
            }else{
                $listdatas[$k]['zhuangtai']='审核中';
            }
            if($v['type']=1){
                $listdatas[$k]['newaddtime']=$v['addtime'];
            }else{
                $listdatas[$k]['newaddtime']=$v['addtime'];
            }


        }

        exit(json_encode(['code'=>200, 'msg'=>'删除成功','listdatas'=>$listdatas,'pagename'=>$pagename]));
    }else{
        exit(json_encode(['code'=>400, 'msg'=>'无权限查看','listdatas'=>$where]));
    }
}else{

}

}else{

    exit(json_encode(['code'=>520, 'msg'=>'没有找到数据']));
}


    }else{
        exit(json_encode(['code'=>500, 'msg'=>'数据有误']));
    }






    }

    /**
     * 会员登录
     *
     * @param string $account  账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = $this->request->request('account');
        $password = $this->request->request('password');
        if (!$account || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 手机验证码登录
     *
     * @param string $mobile  手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (!Sms::check($mobile, $captcha, 'mobilelogin')) {
            $this->error(__('Captcha is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if ($user) {
            if ($user->status != 'normal') {
                $this->error(__('Account is locked'));
            }
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        } else {
            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile, []);
        }
        if ($ret) {
            Sms::flush($mobile, 'mobilelogin');
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }



    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }

    /**
     * 修改会员个人信息
     *
     * @param string $avatar   头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $bio      个人简介
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->request('username');
        $nickname = $this->request->request('nickname');
        $bio = $this->request->request('bio');
        $avatar = $this->request->request('avatar', '', 'trim,strip_tags,htmlspecialchars');
        if ($username) {
            $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Username already exists'));
            }
            $user->username = $username;
        }
        $user->nickname = $nickname;
        $user->bio = $bio;
        $user->avatar = $avatar;
        $user->save();
        $this->success();
    }

    /**
     * 修改邮箱
     *
     * @param string $email   邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->request('captcha');
        if (!$email || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @param string $mobile   手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Mobile already exists'));
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @param string $platform 平台名称
     * @param string $code     Code码
     */
    public function third()
    {
        $url = url('user/index');
        $platform = $this->request->request("platform");
        $code = $this->request->request("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error(__('Invalid parameters'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo'  => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 重置密码
     *
     * @param string $mobile      手机号
     * @param string $newpassword 新密码
     * @param string $captcha     验证码
     */
    public function resetpwd()
    {
        $type = $this->request->request("type");
        $mobile = $this->request->request("mobile");
        $email = $this->request->request("email");
        $newpassword = $this->request->request("newpassword");
        $captcha = $this->request->request("captcha");
        if (!$newpassword || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if ($type == 'mobile') {
            if (!Validate::regex($mobile, "^1\d{10}$")) {
                $this->error(__('Mobile is incorrect'));
            }
            $user = \app\common\model\User::getByMobile($mobile);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Sms::check($mobile, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Sms::flush($mobile, 'resetpwd');
        } else {
            if (!Validate::is($email, "email")) {
                $this->error(__('Email is incorrect'));
            }
            $user = \app\common\model\User::getByEmail($email);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Ems::check($email, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Ems::flush($email, 'resetpwd');
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }
}

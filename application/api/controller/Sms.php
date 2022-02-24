<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Sms as Smslib;
use app\common\model\User;
use think\Hook;
use Qcloud\sms\SmsSingleSender;


/**
 * 手机短信接口
 */
class Sms extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    /**
     * 发送验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     */
    public function send()
    {
//        $mobile = $this->request->request("mobile");
        $mobile='18980818615';
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';

        if (!$mobile || !\think\Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('手机号不正确'));
        }
        $last = Smslib::get($mobile, $event);
        if ($last && time() - $last['createtime'] < 60) {
            $this->error(__('发送频繁'));
        }
        $ipSendTotal = \app\common\model\Sms::where(['ip' => $this->request->ip()])->whereTime('createtime', '-1 hours')->count();
        if ($ipSendTotal >= 5) {
            $this->error(__('发送频繁'));
        }
        if ($event) {
            $userinfo = User::getByMobile($mobile);
            if ($event == 'register' && $userinfo) {
                //已被注册
                $this->error(__('已被注册'));
            } elseif (in_array($event, ['changemobile']) && $userinfo) {
                //被占用
                $this->error(__('已被占用'));
            } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                //未注册
                $this->error(__('未注册'));
            }
        }
        if (!Hook::get('sms_send')) {
            $this->error(__('请在后台插件管理安装短信验证插件'));
        }
        $ret = Smslib::send($mobile, null, $event);
        if ($ret) {
            $this->success(__('发送成功'));
        } else {
            $this->error(__('发送失败，请检查短信配置是否正确'));
        }
    }

    /**
     * 检测验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     * @param string $captcha 验证码
     */
    public function check()
    {
        $mobile = $this->request->request("mobile");
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';
        $captcha = $this->request->request("captcha");

        if (!$mobile || !\think\Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('手机号不正确'));
        }
        if ($event) {
            $userinfo = User::getByMobile($mobile);
            if ($event == 'register' && $userinfo) {
                //已被注册
                $this->error(__('已被注册'));
            } elseif (in_array($event, ['changemobile']) && $userinfo) {
                //被占用
                $this->error(__('已被占用'));
            } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                //未注册
                $this->error(__('未注册'));
            }
        }
        $ret = Smslib::check($mobile, $captcha, $event);
        if ($ret) {
            $this->success(__('成功'));
        } else {
            $this->error(__('验证码不正确'));
        }
    }




    /**
     * 发送验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     */

    public function tenxun(){
        // 短信应用 SDK AppID
        $appid = 1400605895; // SDK AppID 以1400开头
        // 短信应用 SDK AppKey
        $appkey = "87570dfb5e76ba732f3ba42d79c69aa8";
        // 需要发送短信的手机号码
        $phoneNumbers = 18782137307;
        // 短信模板 ID，需要在短信控制台中申请
        $templateId = 1224952;  // NOTE: 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
        $smsSign = "小泸公享"; // NOTE: 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请

        try {
            $ssender = new SmsSingleSender($appid, $appkey);
//            $params = [rand(1000, 9999)];//生成随机数
            $params[0]='技术李东明';
            $result = $ssender->sendWithParam("86", $phoneNumbers, $templateId, $params, $smsSign, "", "");
            $rsp = json_decode($result);
            return json(["result"=>$rsp->result,"code"=>$params,'res'=>$rsp]);
        } catch(\Exception $e) {
            echo var_dump($e);
        }
    }






    /**
     * 示例
     */
    public function confirm_distributor()
    {
        $d = input('post.');
        $wx['openid']='obZ3e5Xm-e_QH4FtabZ9gWS5W5qU';
        $data = ['id'=>'12','appoint'=>'123','status'=>3];
//        if($d['type'] == 1){
//            $ret = OrderGoods::update($data);
//            $page = 'pages/orderDetails/orderDetails?id='.$d['id'].'&order_id='.$d['orderid'].'&act=2&order_act=1';
//        }
//        if($d['type'] == 2){
//            $ret = IntegralOrderGoods::update($data);
            $page = 'pages/integralorderDetails/integralorderDetails?id=12&order_id=123&act=1&order_act=2';
//        }

//        $franchise = Db::name('franchise')->where('id','eq',$d['franchise_id'])->find();
        $franchise['create_time']=1;
        $franchise['shop_name']=2;
        $franchise['orderid']=3;
        //模板消息
        $data  = [
            'keyword1'=>[ //下单时间
                'value'=> $franchise['create_time'],
            ],
            'keyword2'=>[ //所属店铺
                'value'=> $franchise['shop_name'],
            ],
            'keyword3'=>[ //订单编号
                'value'=> $franchise['orderid'],
            ],

        ];
        $template_id = 's9T0t2w6R6v-KVTnwS2aYjTzCyrdJRfk9Kq_IYPkffs';
        $res = $this->send_template($wx['openid'],$template_id,$d['formid'],$data,$d['id'],$d['orderid'],2,1,$page);
        return json_encode(array('code'=>200,'msg'=>'kkkk'));
    }

    /**
     * [send_template 推送模板消息]
     * @param  [type] $openid      [openid]
     * @param  [type] $template_id [模板消息id]
     * @param  [type] $formid      [表单id]
     * @param  [type] $data        [内容]
     * @param  [type] $id          [商品id]
     * @param  [type] $order_id    [订单编号]
     * @param  [type] $act         [查看标识]
     * @param  [type] $order_act   [查看标识]
     * @return [type]              [description]
     */
    function send_template($openid,$template_id,$formid,$data,$id,$order_id,$act='2',$order_act='1',$page){
        //form_id 表单提交场景下，为submit事件带上的formId；支付场景下，为本次支付的prepay_id
        //获取token值

        $config['x_appid']='wxe2c6c3ed32400877';
            $config['x_appsecret']='4afd2c8d56a521517d505079c1092ccd';
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$config['x_appid']."&secret=".$config['x_appsecret'];
        $result = curl_get($url);
        $access_token = $result['access_token'];
        //拼装URL
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token;
        //拼装模板消息
        // $data  = [
        //     'keyword1'=>[
        //         'value'=>"“".$textname."”",
        //         'color'=>'#000000'
        //     ],
        // ];
        $template_data=[
            'touser'=>$openid,
            'template_id'=>$template_id,
            'page'=>$page,
            'form_id'=>$formid,
            'data'=>$data,
            'emphasis_keyword'=>'',
        ];
        $template_data = json_encode($template_data);
        $result = curl_post_https($url,$template_data);
        return $result;
    }

    /* PHP CURL HTTPS POST */
    function curl_post_https($url,$data){ // 模拟提交数据函数
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据，json格式
    }

















}

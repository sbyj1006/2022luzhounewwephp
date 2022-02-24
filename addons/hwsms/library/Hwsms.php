<?php

namespace addons\hwsms\library;

use fast\Http;

/**
 * 华为云短信类
 */
class Hwsms
{
    private $_params = [];
    public $error = '';
    protected $config = [];
    protected static $instance;

    public function __construct($options = [])
    {
        if ($config = get_addon_config('hwsms')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
    }

    /**
     * 单例
     * @param array $options 参数
     * @return Hwsms
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * 设置签名
     * @param string $sign
     * @return Hwsms
     */
    public function sign($sign = '')
    {
        $this->_params['signature'] = $sign;
        return $this;
    }

    /**
     * 设置参数
     * @param array $param
     * @return Hwsms
     */
    public function param(array $param = [])
    {
        foreach ($param as $k => &$v) {
            $v = (string)$v;
        }
        unset($v);
        if ($param) {
            $this->_params['templateParas'] = json_encode(array_values($param));
        }
        return $this;
    }

    /**
     * 设置模板
     * @param string $code 短信模板
     * @return Hwsms
     */
    public function template($code = '')
    {
        $this->_params['templateId'] = $code;
        return $this;
    }

    /**
     * 接收手机
     * @param string $mobile 手机号码
     * @return Hwsms
     */
    public function mobile($mobile = '')
    {
        $this->_params['to'] = $mobile;
        return $this;
    }

    /**
     * 发送方
     * @param string $code 国内短信填写为短信平台为短信签名分配的通道号码
     * @return Hwsms
     */
    public function from($code = '')
    {
        $this->_params['from'] = $code;
        return $this;
    }

    /**
     * 立即发送
     * @return boolean
     */
    public function send()
    {
        $this->error = '';
        $params = $this->_params();
        $options = [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
                'X-WSSE: ' . $this->buildWsseHeader()
            ]
        ];
        $ret = Http::sendRequest('https://rtcsms.cn-north-1.myhuaweicloud.com:10743/sms/batchSendSms/v1', $params, 'POST', $options);
        if ($ret['ret'] !== false) {
            $res = (array)json_decode($ret['msg'], true);
            if (isset($res['code']) && $res['code'] == '000000') {
                return true;
            }
            $this->error = isset($res['description']) ? $res['description'] : 'Invalid result';
        } else {
            $this->error = 'Network error';
        }

        if (config('app_debug')) {
            \think\Log::record('hwsms:' . $this->error);
        }
        return false;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 获取发送的参数信息
     * @return array
     */
    private function _params()
    {
        $params = array_merge([
            'signature' => isset($this->config['sign']) ? $this->config['sign'] : '',
            'from'      => isset($this->config['sender']) ? $this->config['sender'] : '',
        ], $this->_params);
        if (isset($params['to']) && $params['to']) {
            $toArr = explode(',', $params['to']);
            foreach ($toArr as $index => &$item) {
                if (substr($item, 0, 1) !== '+') {
                    $item = '+86' . $item;
                }
            }
            $params['to'] = implode(',', $toArr);
        }
        return $params;
    }

    /**
     * 构造X-WSSE参数值
     * @return string
     */
    function buildWsseHeader()
    {
        $now = date('Y-m-d\TH:i:s\Z'); //Created
        $nonce = uniqid(); //Nonce
        $base64 = base64_encode(hash('sha256', ($nonce . $now . $this->config['secret'])));
        return sprintf("UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"", $this->config['key'], $base64, $nonce, $now);
    }

}

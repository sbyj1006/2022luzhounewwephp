<?php

return array(
    array(
        'name'    => 'key',
        'title'   => '应用key',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => 'your key',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'secret',
        'title'   => '密钥secret',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => 'your secret',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'sender',
        'title'   => '发送者',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => 'csms12345678',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '国内短信填写为短信平台为短信签名分配的通道号码',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'sign',
        'title'   => '签名',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => 'your sign',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'template',
        'title'   => '短信模板',
        'type'    => 'array',
        'content' =>
            array(),
        'value'   =>
            array(
                'register'     => '8ff55xxxxxxxxxxxxxxxxxxx',
                'resetpwd'     => '8ff55xxxxxxxxxxxxxxxxxxx',
                'changepwd'    => '8ff55xxxxxxxxxxxxxxxxxxx',
                'changemobile' => '8ff55xxxxxxxxxxxxxxxxxxx',
                'profile'      => '8ff55xxxxxxxxxxxxxxxxxxx',
                'notice'       => '8ff55xxxxxxxxxxxxxxxxxxx',
            ),
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => '__tips__',
        'title'   => '温馨提示',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => '应用key和secret可以通过 https://console.huaweicloud.com/message/#/msgSms/applicationManage 获取',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ),
);

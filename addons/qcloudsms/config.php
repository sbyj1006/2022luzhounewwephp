<?php

return array (
  0 => 
  array (
    'name' => 'appid',
    'title' => '应用AppID',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '1400605895',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'appkey',
    'title' => '应用AppKEY',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '87570dfb5e76ba732f3ba42d79c69aa8',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'voiceAppid',
    'title' => '语音短信AppID',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '使用语音短信必须设置',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => 'voiceAppkey',
    'title' => '语音短信AppKEY',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '使用语音短信必须设置',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  4 => 
  array (
    'name' => 'sign',
    'title' => '签名',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '小泸公享',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  5 => 
  array (
    'name' => 'isVoice',
    'title' => '是否使用语音短信',
    'type' => 'radio',
    'content' => 
    array (
      0 => '否',
      1 => '是',
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  6 => 
  array (
    'name' => 'isTemplateSender',
    'title' => '是否使用短信模板发送',
    'type' => 'radio',
    'content' => 
    array (
      0 => '否',
      1 => '是',
    ),
    'value' => '1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  7 => 
  array (
    'name' => 'template',
    'title' => '短信模板',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'register' => '1224984',
      'resetpwd' => '1224952',
      'changepwd' => '',
      'profile' => '',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  8 => 
  array (
    'name' => 'voiceTemplate',
    'title' => '语音短信模板',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'register' => '',
      'resetpwd' => '',
      'changepwd' => '',
      'profile' => '',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);

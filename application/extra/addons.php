<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'sms_send' => 
    array (
      0 => 'hwsms',
      1 => 'qcloudsms',
      2 => 'smsbao',
    ),
    'sms_notice' => 
    array (
      0 => 'hwsms',
      1 => 'qcloudsms',
      2 => 'smsbao',
    ),
    'sms_check' => 
    array (
      0 => 'hwsms',
      1 => 'qcloudsms',
      2 => 'smsbao',
    ),
    'config_init' => 
    array (
      0 => 'nkeditor',
      1 => 'qcloudsms',
    ),
  ),
  'route' => 
  array (
    '/example$' => 'example/index/index',
    '/example/d/[:name]' => 'example/demo/index',
    '/example/d1/[:name]' => 'example/demo/demo1',
    '/example/d2/[:name]' => 'example/demo/demo2',
  ),
);
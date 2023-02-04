<?php
return array (
  0 => 
  array (
    'title' => '支付设置',
    'path' => 'payment',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '支付设置',
        'path' => 'mkPayment/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkPayment/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkPayment/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkPayment/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkPayment/delete',
            'logwriting' => 1,
          ),
        ),
        'sort' => 1,
        'logwriting' => 0,
      ),
    ),
  ),
);
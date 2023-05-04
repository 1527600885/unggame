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
        'title' => '充值奖励设置',
        'path' => 'mkPaymentAwards/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkPaymentAwards/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkPaymentAwards/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkPaymentAwards/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkPaymentAwards/delete',
            'logwriting' => 1,
          ),
        ),
      ),
      1 => 
      array (
        'title' => '支付设置管理',
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
      ),
    ),
  ),
);
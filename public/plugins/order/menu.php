<?php
return array (
  0 => 
  array (
    'title' => '支付订单',
    'path' => 'order',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '支付订单管理',
        'path' => 'mkOrder/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkOrder/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkOrder/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkOrder/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkOrder/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
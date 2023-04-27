<?php
return array (
  0 => 
  array (
    'title' => '客服',
    'path' => 'customer',
    'icon' => 'menu.png',
    'ifshow' => 0,
    'children' => 
    array (
      0 => 
      array (
        'title' => '客服页面语录管理',
        'path' => 'mkCustomerPropaganda/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkCustomerPropaganda/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkCustomerPropaganda/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkCustomerPropaganda/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkCustomerPropaganda/delete',
            'logwriting' => 1,
          ),
        ),
      ),
      1 => 
      array (
        'title' => '客服聊天记录管理',
        'path' => 'mkCustomerLog/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkCustomerLog/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkCustomerLog/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkCustomerLog/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkCustomerLog/delete',
            'logwriting' => 1,
          ),
        ),
      ),
      2 => 
      array (
        'title' => '客服设置管理',
        'path' => 'mkCustomerSet/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkCustomerSet/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkCustomerSet/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkCustomerSet/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkCustomerSet/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
    'sort' => 1,
    'logwriting' => 0,
  ),
);
<?php
return array (
  0 => 
  array (
    'title' => '社交账号类型',
    'path' => 'account',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '未命名管理',
        'path' => 'mkAccountType/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkAccountType/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkAccountType/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkAccountType/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkAccountType/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
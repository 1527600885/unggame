<?php
return array (
  0 => 
  array (
    'title' => '用户签到记录',
    'path' => 'user_sign',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '用户签到记录管理',
        'path' => 'mkUserSign/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkUserSign/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkUserSign/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkUserSign/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkUserSign/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
<?php
return array (
  0 => 
  array (
    'title' => '多图测试',
    'path' => 'test',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '未命名管理',
        'path' => 'mkTest/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkTest/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkTest/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkTest/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkTest/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
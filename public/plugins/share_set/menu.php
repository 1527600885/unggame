<?php
return array (
  0 => 
  array (
    'title' => '分享文案',
    'path' => 'share_set',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '分享设置管理',
        'path' => 'mkShareSet/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkShareSet/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkShareSet/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkShareSet/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkShareSet/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
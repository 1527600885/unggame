<?php
return array (
  0 => 
  array (
    'title' => '游戏管理',
    'path' => 'game',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '游戏品牌管理',
        'path' => 'mkGamebrand/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkGamebrand/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkGamebrand/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkGamebrand/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkGamebrand/delete',
            'logwriting' => 1,
          ),
        ),
      ),
      1 => 
      array (
        'title' => '游戏列表管理',
        'path' => 'mkGamelist/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkGamelist/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkGamelist/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkGamelist/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkGamelist/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
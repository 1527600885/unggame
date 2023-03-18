<?php
return array (
  0 => 
  array (
    'title' => '首页顶部游戏',
    'path' => 'top',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '未命名管理',
        'path' => 'mkTopGame/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkTopGame/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkTopGame/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkTopGame/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkTopGame/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
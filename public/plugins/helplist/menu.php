<?php
return array (
  0 => 
  array (
    'title' => '帮助与支持',
    'path' => 'helplist',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '未命名管理',
        'path' => 'mkHelplist/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkHelplist/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkHelplist/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkHelplist/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkHelplist/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
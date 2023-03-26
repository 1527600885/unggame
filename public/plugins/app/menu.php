<?php
return array (
  0 => 
  array (
    'title' => '版本',
    'path' => 'app',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '版本管理',
        'path' => 'versions/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'versions/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'versions/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'versions/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'versions/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
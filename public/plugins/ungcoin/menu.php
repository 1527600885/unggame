<?php
return array (
  0 => 
  array (
    'title' => '数字货币',
    'path' => 'ungcoin',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '未命名管理',
        'path' => 'mkUngcoinFlow/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkUngcoinFlow/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkUngcoinFlow/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkUngcoinFlow/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkUngcoinFlow/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
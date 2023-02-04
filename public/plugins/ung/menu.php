<?php
return array (
  0 => 
  array (
    'title' => '虚拟币',
    'path' => 'ung',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '虚拟币记录',
        'path' => 'mkUngUserLog/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkUngUserLog/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkUngUserLog/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkUngUserLog/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkUngUserLog/delete',
            'logwriting' => 1,
          ),
        ),
        'sort' => '1',
        'logwriting' => 0,
      ),
      1 => 
      array (
        'title' => '用户持有数量',
        'path' => 'mkUngUser/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkUngUser/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkUngUser/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkUngUser/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkUngUser/delete',
            'logwriting' => 1,
          ),
        ),
        'sort' => 2,
        'logwriting' => 0,
      ),
      2 => 
      array (
        'title' => '虚拟币设置',
        'path' => 'mkUngSet/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkUngSet/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkUngSet/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkUngSet/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkUngSet/delete',
            'logwriting' => 1,
          ),
        ),
        'sort' => '3',
        'logwriting' => 0,
      ),
    ),
  ),
);
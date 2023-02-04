<?php
return array (
  0 => 
  array (
    'title' => '公告',
    'path' => 'notice',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '公告管理',
        'path' => 'mkNotice/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkNotice/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkNotice/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkNotice/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkNotice/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
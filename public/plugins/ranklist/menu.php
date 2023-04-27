<?php
return array (
  0 => 
  array (
    'title' => '排行版数据',
    'path' => 'ranklist',
    'icon' => 'menu.png',
    'ifshow' => 0,
    'children' => 
    array (
      0 => 
      array (
        'title' => '排行管理',
        'path' => 'mkRanklist/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkRanklist/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkRanklist/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkRanklist/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkRanklist/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
    'sort' => 1,
    'logwriting' => 0,
  ),
);
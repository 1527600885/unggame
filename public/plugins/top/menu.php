<?php
return array (
  0 => 
  array (
    'title' => '直播游戏',
    'path' => 'top',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '直播游戏管理',
        'path' => 'mkTopLivegame/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkTopLivegame/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkTopLivegame/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkTopLivegame/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkTopLivegame/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
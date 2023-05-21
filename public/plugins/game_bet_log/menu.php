<?php
return array (
  0 => 
  array (
    'title' => '游戏实时记录',
    'path' => 'game_bet_log',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '游戏实时记录管理',
        'path' => 'mkGameBetLog/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkGameBetLog/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkGameBetLog/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkGameBetLog/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkGameBetLog/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
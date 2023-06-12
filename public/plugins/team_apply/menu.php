<?php
return array (
  0 => 
  array (
    'title' => '代理',
    'path' => 'team_apply',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '未命名管理',
        'path' => 'mkTeamApply/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkTeamApply/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkTeamApply/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkTeamApply/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkTeamApply/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
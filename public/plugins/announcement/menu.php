<?php
return array (
  0 => 
  array (
    'title' => '公告管理',
    'path' => 'announcement',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '公告管理',
        'path' => 'mkAnnouncement/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkAnnouncement/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkAnnouncement/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkAnnouncement/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkAnnouncement/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
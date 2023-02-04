<?php
return array (
  0 => 
  array (
    'title' => '提现记录',
    'path' => 'withdrawal',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '提现记录管理',
        'path' => 'mkWithdrawal/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkWithdrawal/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkWithdrawal/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkWithdrawal/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkWithdrawal/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
<?php
return array (
  0 => 
  array (
    'title' => '提现信息',
    'path' => 'withdrawal_info',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '用户卡号信息管理',
        'path' => 'mkWithdrawalInfo/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkWithdrawalInfo/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkWithdrawalInfo/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkWithdrawalInfo/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkWithdrawalInfo/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
<?php
return array (
  0 => 
  array (
    'title' => '货币设置',
    'path' => 'currency',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '货币设置管理',
        'path' => 'mkCurrencyAll/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkCurrencyAll/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkCurrencyAll/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkCurrencyAll/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkCurrencyAll/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
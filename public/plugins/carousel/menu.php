<?php
return array (
  0 => 
  array (
    'title' => '首页管理',
    'path' => 'carousel_chart',
    'icon' => 'menu.png',
    'ifshow' => 1,
    'children' => 
    array (
      0 => 
      array (
        'title' => '首页轮播图管理',
        'path' => 'mkCarouselChart/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '查看',
            'path' => 'mkCarouselChart/index',
          ),
          1 => 
          array (
            'title' => '发布',
            'path' => 'mkCarouselChart/save',
            'logwriting' => 1,
          ),
          2 => 
          array (
            'title' => '编辑',
            'path' => 'mkCarouselChart/update',
            'logwriting' => 1,
          ),
          3 => 
          array (
            'title' => '删除',
            'path' => 'mkCarouselChart/delete',
            'logwriting' => 1,
          ),
        ),
      ),
    ),
  ),
);
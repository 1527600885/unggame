<?php
return array (
  0 => 
  array (
    'title' => '流量统计',
    'path' => 'index/index',
    'icon' => 'menu.png',
    'ifshow' => 0,
    'children' => 
    array (
      0 => 
      array (
        'title' => '配置管理',
        'path' => 'config/index',
        'ifshow' => 1,
        'sort' => 0,
        'logwriting' => 0,
      ),
      1 => 
      array (
        'title' => '网站概况',
        'path' => 'index/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '单图形',
            'path' => 'index/chart',
          ),
          1 => 
          array (
            'title' => '单表格',
            'path' => 'index/table',
          ),
          2 => 
          array (
            'title' => '新老用户',
            'path' => 'index/newuser',
          ),
        ),
      ),
      2 => 
      array (
        'title' => '流量分析',
        'path' => 'flow/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '实时访客',
            'path' => 'flow/visitors',
            'ifshow' => 1,
          ),
          1 => 
          array (
            'title' => '实时访客图表',
            'path' => 'flow/visitorsChart',
          ),
          2 => 
          array (
            'title' => '趋势分析',
            'path' => 'flow/trend',
            'ifshow' => 1,
          ),
        ),
      ),
      3 => 
      array (
        'title' => '来源分析',
        'path' => 'source/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '全部来源',
            'path' => 'source/all',
            'ifshow' => 1,
          ),
          1 => 
          array (
            'title' => '全部来源图表',
            'path' => 'source/allChart',
          ),
          2 => 
          array (
            'title' => '搜索引擎',
            'path' => 'source/keywordForm',
            'ifshow' => 1,
          ),
          3 => 
          array (
            'title' => '搜索引擎图表',
            'path' => 'source/keywordFormChart',
          ),
          4 => 
          array (
            'title' => '搜索词',
            'path' => 'source/keyword',
            'ifshow' => 1,
          ),
          5 => 
          array (
            'title' => '搜索词图表',
            'path' => 'source/keywordChart',
          ),
          6 => 
          array (
            'title' => '外部链接',
            'path' => 'source/referrer',
            'ifshow' => 1,
          ),
          7 => 
          array (
            'title' => '外部链接图表',
            'path' => 'source/referrerChart',
          ),
        ),
      ),
      4 => 
      array (
        'title' => '访问分析',
        'path' => 'visit/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '受访页面',
            'path' => 'visit/url',
            'ifshow' => 1,
          ),
          1 => 
          array (
            'title' => '受访页面图表',
            'path' => 'visit/urlChart',
          ),
          2 => 
          array (
            'title' => '入口页面',
            'path' => 'visit/entryUrl',
            'ifshow' => 1,
          ),
          3 => 
          array (
            'title' => '入口页面图表',
            'path' => 'visit/entryUrlChart',
          ),
        ),
      ),
      5 => 
      array (
        'title' => '访客分析',
        'path' => 'visitor/index',
        'ifshow' => 1,
        'children' => 
        array (
          0 => 
          array (
            'title' => '地域分布',
            'path' => 'visitor/area',
            'ifshow' => 1,
          ),
          1 => 
          array (
            'title' => '地域分布图表',
            'path' => 'visitor/areaChart',
          ),
          2 => 
          array (
            'title' => '系统环境',
            'path' => 'visitor/os',
            'ifshow' => 1,
          ),
          3 => 
          array (
            'title' => '系统环境图表',
            'path' => 'visitor/osChart',
          ),
          4 => 
          array (
            'title' => '新老访客',
            'path' => 'visitor/newuser',
            'ifshow' => 1,
          ),
        ),
      ),
    ),
    'sort' => '1',
    'logwriting' => 0,
  ),
);
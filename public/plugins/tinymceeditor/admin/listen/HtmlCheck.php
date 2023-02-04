<?php
// +----------------------------------------------------------------------
// | OneKeyAdmin [ Believe that you can do better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2023 http://onekeyadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MUKE <513038996@qq.com>
// +----------------------------------------------------------------------
namespace plugins\tinymceeditor\admin\listen;

class HtmlCheck
{
    public function handle($response)
    {
        $bind = "\n".'<script type="text/javascript" charset="utf-8" src="/plugins/tinymceeditor/static/tinymce/tinymce.min.js"></script>';
        $bind .= "\n".'<script type="text/javascript" charset="utf-8" src="/plugins/tinymceeditor/static/admin/tinymceeditor.js"></script>'."\n";
        $content = str_replace('</head>', '</head>' . $bind, $response->getContent());
        $response->content($content);
    }
}
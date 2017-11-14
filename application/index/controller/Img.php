<?php
namespace app\index\controller;

use think\Db;

class Img extends Common
{
    /**
     *轮播图
     **/
    public function image_list(){
        $img=Db::name('img')->where("is_show",1)->select();
        $this->get_msg(100,$img);
    }
}

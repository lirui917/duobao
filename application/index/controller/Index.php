<?php
namespace app\index\controller;

use think\Db;

class Index extends Common
{
    public function index()
    {
        return 12223;
    }
    public function geturl(){
        $sql="select * from ecs_keywords";
        $system=Db::name('keywords')->find();
//        $system=Db::query($sql);
        if ($system){
            $this->get_msg('100',$system);
        }else{
            $this->get_msg('109','没有配置');
        }

    }
}

<?php
namespace app\index\controller;

use think\Db;

class Test extends Common
{
    public function index()
    {
        return 12223;
    }
    public function geturl(){
        $sql="select * from ecs_keywords";
//        $system=Db::table('keywords')->find();
        $system=Db::query($sql);
        if ($system){
            echo $this->get_msg('100',$system);
        }else{
            echo $this->get_msg('109','没有配置');
        }

    }
    public function add(){
        return 1223222;
    }
}

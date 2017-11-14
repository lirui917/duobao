<?php
namespace app\index\controller;

use think\Db;

class Hotsearch extends Common
{
   /**
    * [hotsearch_list description]热搜列表
    * @return [type] [description]
    */
    public function hotsearch_list(){
        $list=Db::name('hotsearch')->where('is_show',1)->select();
        if($list){
        	$this->get_msg('100',$list);
        }
    }

}

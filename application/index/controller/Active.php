<?php
namespace app\index\controller;

use think\Db;

class Active extends Common
{
    /**
     优惠券
     **/
    public function active_list(){
        $active=Db::name('favourable_activity')->where("seller_id",1)->select();
        foreach ($active as &$v){
            $v['start_time']=date('Y-m-d H:i:s',$v['start_time']);
            $v['end_time']=date('Y-m-d H:i:s',$v['end_time']);
            unset($v);
        }
        $this->get_msg(100,$active);
    }
    public function active_add(){
        $act_id=$_GET['val'];
        $user_id=isset($_GET['user_id'])?$_GET['user_id']:1;
        $data=Db::name("user_activity")->where("user_id=$user_id and act_id=$act_id")->find();
        if($data){
            $this->get_msg(20,"你已经有优惠券，下次才能领取");;
        }else{
            $id=array(
                'act_id'=>$act_id,
                'user_id'=>$user_id,
                'user_uptime'=>time(),
                'status'=>1
            );
            $add=Db::name("user_activity")->insert($id);
            if($add){
                $this->get_msg(100,"领取优惠券成功");
            }else{
                $this->get_msg(30,"领取优惠券失败");
            }
        }
    }

}

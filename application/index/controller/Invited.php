<?php
namespace app\index\controller;

use think\Db;

class Invited extends Common
{
   /**
     * [invited_list description]用户邀请注册信息
     * @return array [first]一级用户列表
     * @return array [second]二级用户列表
     * @return array [third]三级级用户列表
     * @return array [number]各级用户人数及消费人数
     */
    public function invited_list(){
        $user_id=isset($_GET['user_id'])?$_GET['user_id']:'';
        if($user_id==''){
           $this->get_msg('104','缺少参数');die;
        } 
        $data['number']['first']['total']=$data['number']['first']['is_pay']=0;
        $data['number']['second']['total']=$data['number']['second']['is_pay']=0;
        $data['number']['third']['total']=$data['number']['third']['is_pay']=0;        
        //一级用户
        $data['first']=Db::name('admin_invited')->alias('v')->join('admin_user u','v.beinviter_id=u.user_id')->field('v.invited_id,v.user_id,v.beinviter_id,v.addtime,v.status,u.user_name')->where('v.user_id',$user_id)->select();
        if($data['first']){
            $data['number']['first']=$this->getNum($data['first']);
            foreach ($data['first'] as $key => $val) {
                //二级用户
                $info=Db::name('admin_invited')->alias('v')->join('admin_user u','v.beinviter_id=u.user_id')->field('v.invited_id,v.user_id,v.beinviter_id,v.addtime,v.status,u.user_name')->where('v.user_id',$val['beinviter_id'])->select();
                if($info){
                    $data['second'][]=$info;  
                }
            }
        }

        if($data['second']){         
            //三级用户
            foreach ($data['second'] as $k => $v) {
                $arr=$this->getNum($v);
                
                $data['number']['second']['total']+=$arr['total'];
                $data['number']['second']['is_pay']+=$arr['is_pay'];
                foreach ($v as $key => $val) {
                    $res=Db::name('admin_invited')->alias('v')->join('admin_user u','v.beinviter_id=u.user_id')->field('v.invited_id,v.user_id,v.beinviter_id,v.addtime,v.status,u.user_name')->where('v.user_id',$val['beinviter_id'])->select();
                    if($res){
                        $data['third'][]=$res;
                    }
                }
            }
        }
        if($data['third']){
            foreach ($data['third'] as $k => $v) {
                $arr=$this->getNum($v);
                
                $data['number']['third']['total']+=$arr['total'];
                $data['number']['third']['is_pay']+=$arr['is_pay'];
            }
        }
        // var_dump($data);die;
        $this->get_msg("100",$data);
    }
/**
 * [getNum description]统计邀请人数及消费人数
 */
    public function getNum($arr){
        $data['total']=count($arr);
        $i=0;
        foreach ($arr as $key => $val) {
            // echo date('Y-m-d',$val['addtime']);die;
            if($val['status']==1){
                $i++;
            }
        }
        $data['is_pay']=$i;
        return $data;
    }
}

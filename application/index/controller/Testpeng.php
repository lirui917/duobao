<?php
namespace app\index\controller;

use think\Db;

class Testpeng extends Common
{
    public function index()
    {
        return 12223;
    }
    public function recommend(){
        $user_id=1;
        if(empty($user_id)){
        $system=db('user_recommend')->select();    
        $recommend_id=$this->recommend_id($system);
         // var_dump($user);die;
        }else{
        $system=db('user_recommend')->where('user_id='.$user_id)->select();
        $recommend_id=$this->recommend_id($system);    
        }//$recommend_id求出所有推荐的商品类型
        foreach($recommend_id as $v){
        $sql="select  *  from ecs_goods where cat_id=$v order by rand() limit 1";
        $recommend[]=Db::query($sql);
        }
        foreach($recommend as $v){
            $system1[]=$v[0];
        }
        if ($system){
            $this->get_msg('100',$system1);
        }else{
            $this->get_msg('109','没有配置');
        }
    }
    public function recommend_id($system){//求推荐类型id
        foreach($system as $v){
            $arr[$v['type_id']]=$v['number'];
        }
        // echo $count;die;
        for($i=0;$i<5;$i++){
         $recommend_id[]=$this->get_rand($arr);
         }
       return $recommend_id;
    }
    
}

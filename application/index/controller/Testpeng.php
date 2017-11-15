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
        $user_id=isset($_GET['user_id'])?$_GET['user_id']:1;
        //$recommend_id求出所有推荐的商品类型
        if(empty($user_id)){
            $system=db('user_recommend')->select();
            $recommend_id=$this->recommend_id($system);
             // var_dump($user);die;
            }else{
            $system=db('user_recommend')->where('user_id='.$user_id)->select();
            $recommend_id=$this->recommend_id($system);
        }
        //根据推荐的商品类型查询数据库数据
        foreach($recommend_id as $v){
        $sql="select  *  from ecs_goods where cat_id=$v order by rand() limit 1";
        $recommend[]=Db::query($sql);
        }
        foreach($recommend as $v){
            $system1[]=$v[0];
        }
        //移除二维数组中重复的数组值
        $system4=$this->two_reset($system1);
         // var_dump($system4);die;
        if ($system){
            $this->get_msg('100',$system4);
        }else{
            $this->get_msg('109','没有配置');
        }
    }
    public function seller(){
        $sql="select seller_name,seller_id,seller_image from ecs_seller order by rand() limit 10";
        $system=Db::query($sql);
        // var_dump($system);die;
        if ($system){
            $this->get_msg('100',$system);
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
    public function merchant(){
        $goods_id=$_GET['sign'];
        $type_id=db('store_goods')->field('store_id')->where("goods_id=".$goods_id)->find(); //商家id
        $goods_id=db('store_goods')->field('goods_id')->where("store_id=".$type_id['store_id'])->select(); //求商家对应的商品id
        $goods_id1='';
        foreach($goods_id as $v){
            $goods_id1.=$v['goods_id'].",";
        }
        $goods_id1=rtrim($goods_id1, ','); 
        $goods=db('goods')->where("goods_id in (".$goods_id1.")")->select();
        // var_dump($goods);die;
        if ($goods){
            $this->get_msg('100',$goods);
        }else{
            $this->get_msg('109','没有配置');
        }
    }
    public function two_reset($system1){//移除二维数组中重复的数组值
        foreach($system1 as $k=>$v){
            $arr[$k]=$v['goods_id'];
        }
        $system3=array_unique($arr);
        $key=array_keys($system3);
        foreach($key as $v){
            $system4[]=$system1[$v];
        }
        return $system4;
    }

    public function get_rand($proArr) { //一维数组：产生概率
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr); //总数为121
        // var_dump($proSum);die;
        //概率数组循环
        foreach ($proArr as $key => $proCur) { //$key指的是类型
            $randNum = mt_rand(1, $proSum); //抽取随机数
            if ($randNum <= $proCur) { //1-121数< $v
                $result = $key; //result=类型
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
}

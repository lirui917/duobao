<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

class Rush extends Common
{
     /**
     * 一元夺宝
     */
    public function rush()
    {
        $sql="SELECT act_id,act_name,act_price,goods_img,start_time,end_time from ecs_goods,ecs_goods_activity where ecs_goods_activity.goods_id = ecs_goods.goods_id ";
        $goods=Db::query($sql); 
        $goods['time']=time();
        if(!empty($goods)){
            $goods['error'] = 200;
        }
        echo $_GET['callback'].'('.json_encode($goods).')';
    }

   /**
   *一元夺宝详情
   */
   public function detail()
   {
        //夺购商品id base64加密
        $act_id=isset($_GET['act_id'])?$_GET['act_id']:"";
        if($act_id == "MA=="){
            echo $this->get_msg('103','夺购商品id为空');exit;
        }
        //act_id base64解密
        $act_ids=base64_decode($act_id);
        //商品属性
        $sql="SELECT act_name,goods_name,act_priod,act_price,act_join,act_sum,goods_id from ecs_goods_activity where act_id = $act_ids ";
        $system=Db::query($sql); 
        if($system[0]['goods_id'] == ""){
            echo $this->get_msg('103','商品详情id为空');exit;
        }
        $goods_id=$system[0]['goods_id'];
        //商品详情相册
        $img_url="SELECT img_url from ecs_goods_gallery where goods_id = $goods_id ";
        $imgUrl=Db::query($img_url); 
        $system[0]['img_url']=$imgUrl;
        $system[0]['act_remain']=$system[0]['act_sum']-$system[0]['act_join'];
        //成功码
        $system[0]['error']=200;
        echo $_GET['callback'].'('.json_encode($system[0]).')';
   }

}
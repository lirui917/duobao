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
     //查询热门推荐商家
    public function remen(){
        $data=Db::table('ecs_seller')->where('seller_type',17)->limit(6)->select();
        echo $_GET['callback'].'('.json_encode($data).')';
    }
    //查询商家所属商品
    public function remen_details(){
        $data['goods']=Db::table('ecs_goods')->where("goods_seller_id",$_GET['id'])->select();
        $data['seller']=Db::table('ecs_seller')->where("seller_id",$_GET['id'])->find();
        $data['seller']['seller_starttime']=date('H:i',strtotime(date('Y-m-d'))+$data['seller']['seller_starttime']);
        $data['seller']['seller_endtime']=date('H:i',strtotime(date('Y-m-d'))+$data['seller']['seller_endtime']);
        echo $_GET['callback'].'('.json_encode($data).')';
    }
    //商品信息
    public function general(){
        $data['goods']=Db::table('ecs_goods')->where("goods_id",$_GET['id'])->find();
        $goods_attr=Db::query("select * from ecs_goods_attr_type as gat left JOIN ecs_goods_attr as ga on ga.goods_attr_type_id=gat.goods_attr_type_id where ga.goods_id=".$_GET['id']." ");
        $data['goods_attr']=[];
        foreach ($goods_attr as $key => $val) {
            $num=$key%4;
            //注释 懒得写
            $data['goods_attr'][$val['goods_attr_type_name']][$num]['goods_attr_name']=$val['goods_attr_name'];
            $data['goods_attr'][$val['goods_attr_type_name']][$num]['goods_attr_price']=$val['goods_attr_price'];
            $data['goods_attr'][$val['goods_attr_type_name']][$num]['goods_attr_id']=$val['goods_attr_id'];
        }
        foreach ($data['goods_attr'] as $key => $val) {
            $count=count($val);
            $data['goods_attr'][$key]['lenthg']=$count;
        }
        echo $_GET['callback'].'('.json_encode($data).')';
    }
    /*
     * attr_id array(attr_id)  数组，包含attr_id
     * user_id int   用户id
     * goods_id int  商品id
     * goods_num int 商品数量
     */
    //添加购物车
    public function addcart(){
        //接数据
        $data=json_decode($_GET['str'],true);
        //根据attr_id查询goods_attr表
        foreach ($data['attr_id'] as $key => $val) {
            $goods['price'][]=Db::table('ecs_goods_attr')->where("goods_attr_id",$val)->find();
        };
        $goods_attr=Db::query("select count(goods_attr_type_id) from ecs_goods_attr_type where goods_id={$data['goods_id']}");
        if ($goods_attr[0]['count(goods_attr_type_id)'] != count($goods['price'])) {
            $back['error']=2;
            echo $_GET['callback'].'('.json_encode($back).')';die;
        }
        //根据goods_id查询goods表数据
        $goods['goods']=Db::table('ecs_goods')->field('goods_name,shop_price,market_price')->where("goods_id",$data['goods_id'])->find();

        //根据attr_id计算总价格
        $add['goods_price']=0;
        foreach ($goods['price'] as $key => $val) {
            if ($val['goods_id']!=$data['goods_id']) {
                $back['error']=1;
                echo $_GET['callback'].'('.json_encode($back).')';die;
            }
           $add['goods_price']+=$val['goods_attr_price'];
        }

        $add['goods_price']+=$goods['goods']['shop_price'];
        $add['user_id']=$data['id'];
        $add['goods_id']=$data['goods_id'];
        $add['goods_name']=$goods['goods']['goods_name'];
        $add['market_price']=$goods['goods']['market_price'];
        $add['goods_number']=$data['goods_num'];
        $add['goods_attr_id']=implode(',', $data['attr_id']);
        //拼接goods_name
        foreach ($goods['price'] as $key => $val) {
            $goods_name[]=$val['goods_attr_name'];
        }
        $add['goods_attr']=implode(',', $goods_name);
        $add['goods_sn']=$this->goods_sn();

        $find['goods_attr_id']=$add['goods_attr_id'];
        $find['goods_id']=$add['goods_id'];
        $find['user_id']=$add['user_id'];

        $cart=Db::table('ecs_cart')->where($find)->find();
        if (empty($cart)) {
            //添加购物车
            $state=Db::name('cart')->insert($add);
            $back['error']=0;
            echo $_GET['callback'].'('.json_encode($back).')';
        }else{
            Db::table('ecs_cart')->where($find)->update(['goods_number'=>$data['goods_num']+$cart['goods_number']]);
            $back['error']=0;
            echo $_GET['callback'].'('.json_encode($back).')';
        }
    }
    function goods_sn(){
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $orderSn;
    }
    
}

<?php
namespace app\index\controller;

use think\Db;

class Index extends Common
{

    public function index()
    {
        return 12223;
    }

    /***
     * 查询商家类型
     * @return data array 所有商家
     */
    public function getSellerType(){
        //查询前16条类型
        $types=Db::name('seller_type')->limit(16)->select();
        //拆分数组
        $data=array_chunk($types,8);
        if ($data){
            $this->get_msg('100',$data);
        }else{
            $this->get_msg('110','暂无数据');
        }
    }
    /**
     * 通过商家类型id 查询店铺
     */
    public function getSellerByType(){
        if (isset($_GET['typeid'])&&!empty($_GET['typeid'])){
            $type_id=$_GET['typeid'];   //查询分类下商家
            $sellers=Db::name('seller')->where('seller_type',$type_id)->select();
            $this->returnSellers($sellers,$type_id);
        }else if (isset($_GET['search'])&&!empty($_GET['search'])){
                $search=$this->js_unescape($_GET['search']);
                $sellers=Db::name('seller')->where('seller_name','like','%'.$search.'%')->select();
                $this->returnSellers($sellers,$type_id=false);
        }else{
            $this->get_msg('500','非法请求');
        }

    }

    /**
     * @param $str     string  经过escape 编码后的值
     * @return string   字符串 解码后的值
     */

    public function js_unescape($str){
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++)
        {
        if ($str[$i] == '%' && $str[$i+1] == 'u')
        {
        $val = hexdec(substr($str, $i+2, 4));
        if ($val < 0x7f) $ret .= chr($val);
        else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
        else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
        $i += 5;
        }
        else if ($str[$i] == '%')
        {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        }
        else $ret .= $str[$i];
        }
        return $ret;
    }

    /**
     * @param $sellers 商家数据 array
     * @param $type_id 分类id
     */

    public function returnSellers($sellers,$type_id){
        if ($sellers){
            foreach ($sellers as $k=>&$v){
                $v['seller_endtime']=date('H:i:s',$v['seller_endtime']);
                $v['seller_starttime']=date('H:i:s',$v['seller_starttime']);
                unset($v);
            }
            $data['sellers']=$sellers;
            if (isset($type_id)){
                //查询二级分类
                $category=Db::name('seller_category')->where('seller_type_id',$type_id)->select();
                if ($category){
                    $data['category']=$category;
                }
            }
            $this->get_msg('100',$data);
        }else{
            $this->get_msg('110','暂无数据');
        }
    }


    /**
     *获取商家详情 通过商家ID
     */
    public function getSellerInfoById(){
        if (isset($_GET['sellerid'])){

            $seller_id=$_GET['sellerid'];
            //查询商家详情
            $info=Db::name('seller')->where('seller_id',$seller_id)->find();

            //查询商家左边菜单
            $menus=Db::name('seller_menu')->where('seller_id',$seller_id)->select();
            if ($info){
                $info['seller_endtime']=date('H:i:s',$info['seller_endtime']);
                $info['seller_starttime']=date('H:i:s',$info['seller_starttime']);
                $data['info']=$info;
            }
            //查询首个菜单下商品
            if(!empty($menus)){
                $menu_id=isset($_GET['menu_id'])?$_GET['menu_id']:$menus[0]['menu_id'];
            }else{
                $menu_id='';
            }

            if ($menu_id){
                $goods=Db::name('goods')->where(['goods_seller_id'=>$seller_id,'menu_id'=>$menu_id])->select();
            }else{
                $goods=Db::name('goods')->where('goods_seller_id',$seller_id)->select();
            }

            if ($goods) $data['goods']=$goods;
            if($menus) $data['menus']=$menus;

            $this->get_msg('100',$data);

        }else{

            $this->get_msg('500','非法请求');
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

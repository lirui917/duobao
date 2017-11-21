<?php
namespace app\goods\controller;

use think\Controller;
use think\Db;
use think\Request;
/**
 * Class 
 */
class Index extends Common
{
    public function index()
    {
        return 'Hello';
    }

    public function getUrl()
    {
        $data = Request::instance()->get();
        $callback = Request::instance()->get('callback');
        echo $callback.'('. json_encode($data). ')';
    }

    public function test()
    {
        $goodsInfo = Db::name('goods')->select();
        print_r($goodsInfo);
    }

    /**
     * 积分夺宝展示
     */
    public function Integral_indiana()
    {
        $cid = isset($_GET['cid']) ? $_GET['cid'] : '';
        if(!empty($cid)){
            $integral_in = Db::name('goods_activity')
                ->alias('ga')
                ->join('__GOODS__ g', 'g.goods_id=ga.goods_id')
                ->join('__CATEGORY__ c','g.cat_id = c.cat_id')
                ->where('c.cat_id = '.$cid)
                ->where('is_integral = 1')
                ->select();
            $arr = [];
            foreach ($integral_in as $k => $val) {
                $val['act_surplus'] = $val['act_sum'] - $val['act_join'];
                //计算参加人数所占总人数的百分比
                $val['width'] = round($val['act_join'] / $val['act_sum'] * 100, 2) . "%";
                $arr[$k] = $val;
            }
            $arr['goods'] = $arr;
            //查询所有分类
            $arr['category'] = Db::name('category')
                ->select();
            $callback = input('get.callback');
            echo $callback . '(' . json_encode($arr) . ')';
        }else{
            $integral_in = Db::name('goods_activity')
                ->alias('ga')
                ->join('__GOODS__ g', 'g.goods_id=ga.goods_id')
                ->where('is_integral = 1')
                ->select();
            $arr = [];
            foreach ($integral_in as $k => $val) {
                $val['act_surplus'] = $val['act_sum'] - $val['act_join'];
                //计算参加人数所占总人数的百分比
                $val['width'] = round($val['act_join'] / $val['act_sum'] * 100, 2) . "%";
                $arr[$k] = $val;
            }
            $arr['goods'] = $arr;
            //查询所有分类
            $arr['category'] = Db::name('category')
                ->select();
            $callback = input('get.callback');
            echo $callback . '(' . json_encode($arr) . ')';
        }

    }
    /**
     * 现金夺宝展示
     */
    public function cash_indiana()
    {
        $cid = isset($_GET['cid']) ? $_GET['cid'] : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
        $callback = Request::instance()->get('callback');
        if(!empty($sort)){
//
            switch($sort){
                case "new":
//                    echo $callback.'('.json_encode($sort).')';die;
                    $cash_in = Db::name('goods_activity')
                        ->alias('ga')
                        ->join('__GOODS__ g','ga.goods_id = g.goods_id')
                        ->order('ga.make_time')
                        ->where('is_integral = 0')
                        ->select();
                    $arr = [];
                    foreach($cash_in as $k=>$val) {
                        $val['act_surplus'] = $val['act_sum']-$val['act_join'];
                        //计算参加人数所占总人数的百分比
                        $val['width'] = round($val['act_join']/$val['act_sum'] * 100 ,2)."%";
                        $arr[$k] = $val;
                    }

                    $arr['goods'] = $arr;
                    //查询所有分类
                    $arr['category'] = Db::name('category')
                        ->select();

                    echo $callback.'('.json_encode($arr).')';die;

                case "max":
                    $cash_in = Db::name('goods_activity')
                        ->alias('ga')
                        ->join('__GOODS__ g','ga.goods_id = g.goods_id')
                        ->order('ga.act_price desc')
                        ->where('is_integral = 0')
                        ->select();
                    $arr = [];
                    foreach($cash_in as $k=>$val) {
                        $val['act_surplus'] = $val['act_sum']-$val['act_join'];
                        //计算参加人数所占总人数的百分比
                        $val['width'] = round($val['act_join']/$val['act_sum'] * 100 ,2)."%";
                        $arr[$k] = $val;
                    }
                    $arr['goods'] = $arr;
                    //查询所有分类
                    $arr['category'] = Db::name('category')
                        ->select();
                    echo $callback.'('. json_encode($arr). ')';
                    die;
                case "min":
                    $cash_in = Db::name('goods_activity')
                        ->alias('ga')
                        ->join('__GOODS__ g','ga.goods_id = g.goods_id')
                        ->order('ga.act_price asc')
                        ->where('is_integral = 0')
                        ->select();
                    $arr = [];
                    foreach($cash_in as $k=>$val) {
                        $val['act_surplus'] = $val['act_sum']-$val['act_join'];
                        //计算参加人数所占总人数的百分比
                        $val['width'] = round($val['act_join']/$val['act_sum'] * 100 ,2)."%";
                        $arr[$k] = $val;
                    }
                    $arr['goods'] = $arr;
                    //查询所有分类
                    $arr['category'] = Db::name('category')
                        ->select();
                    echo $callback.'('. json_encode($arr). ')';die;
                    break;
                case "auto":
                    $cash_in = Db::name('goods_activity')
                        ->alias('ga')
                        ->join('__GOODS__ g','ga.goods_id = g.goods_id')
                        ->where('is_integral = 0')
                        ->select();
                    $arr = [];
                    foreach($cash_in as $k=>$val) {
                        $val['act_surplus'] = $val['act_sum']-$val['act_join'];
                        //计算参加人数所占总人数的百分比
                        $val['width'] = round($val['act_join']/$val['act_sum'] * 100 ,2)."%";
                        $arr[$k] = $val;
                    }
                    $arr['goods'] = $arr;
                    //查询所有分类
                    $arr['category'] = Db::name('category')
                        ->select();
                    echo $callback.'('. json_encode($arr). ')';die;
                    break;
            }
        }
        if(!empty($cid)){
            $cash_in = Db::name('goods_activity')
                        ->alias('ga')
                        ->join('__GOODS__ g','ga.goods_id = g.goods_id')
                        ->join('__CATEGORY__ c','g.cat_id = c.cat_id')
                        ->where('c.cat_id = '.$cid)
                        ->where('is_integral = 0')
                        ->select();
            $arr = [];
            foreach($cash_in as $k=>$val) {
                $val['act_surplus'] = $val['act_sum']-$val['act_join'];
                //计算参加人数所占总人数的百分比
                $val['width'] = round($val['act_join']/$val['act_sum'] * 100 ,2)."%";
                $arr[$k] = $val;
            }
            $arr['goods'] = $arr;
            //查询所有分类
            $arr['category'] = Db::name('category')
                ->select();
            echo $callback.'('. json_encode($arr). ')';
        }else{
            $cash_in = Db::name('goods_activity')
                ->alias('ga')
                ->where('is_integral = 0')
                ->join('__GOODS__ g','g.goods_id=ga.goods_id')
                ->select();
            $arr = [];
            foreach($cash_in as $k=>$val) {
                $val['act_surplus'] = $val['act_sum']-$val['act_join'];
                //计算参加人数所占总人数的百分比
                $val['width'] = round($val['act_join']/$val['act_sum'] * 100 ,2)."%";
                $arr[$k] = $val;
            }
            $arr['goods'] = $arr;
            //查询所有分类
            $arr['category'] = Db::name('category')
                ->select();
            echo $callback.'('. json_encode($arr). ')';
        }

    }

    /**
     * 立即夺宝 积分
     */
    public function snatch()
    {
        $gid = input('get.gid'); //商品id
        //判断夺宝时间
        $in_detail = Db::name('goods_activity')
                    ->alias('ga')
                    ->join('__GOODS__ g', 'g.goods_id=ga.goods_id')
                    ->where('ga.is_integral = 1 and ga.goods_id ='.$gid)
//                    ->where('ga.start_time','<',time())
//                    ->where('ga.end_time','>',time())
                    ->find();
//        echo Db::getLastSql();die;
//        print_r($in_detail);exit();
        $in_detail['width'] = round($in_detail['act_join']/$in_detail['act_sum'] * 100 ,2)."%";
        $callback = Request::instance()->get('callback');
        if($in_detail){
            $in_detail['status'] = 1;
            echo $callback.'('. json_encode($in_detail). ')';
        }else{
            $in_detail['status'] = 0;
            $in_detail['error_info'] = "数据查询失败";
            echo $callback.'('. json_encode($in_detail). ')';
        }

    }

    /**
     *
     */
    public function pact_info()
    {
        $gid = input('get.gid'); //商品id
        $info = Db::name('goods')
                ->alias('g')
                ->field('goods_desc')
                ->where('g.goods_id ='.$gid)
                ->find();
//        print_r($info);exit();
        $callback = Request::instance()->get('callback');
        if($info){
            $info['status'] = 1;
            echo $callback.'('. json_encode($info). ')';
        }else{
            $info['status'] = 0;
            $info['error_info'] = "数据查询失败";
            echo $callback.'('. json_encode($info). ')';
        }
    }

    /**
     * 生成订单 并返回订单信息 现金 2
     */
    public function order_()
    {
        $gid = input('get.gid');
        $goods_amount = isset($_GET['goods_amount']) ? $_GET['goods_amount'] : '';
        $uid = 1;
        //查询 user_address
        $user_address = Db::name('user_address')
            ->alias('ua')
            ->join('__USERS__ u','u.user_id=ua.user_id')
            ->find();
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        //入订单表
        Db::name('order_info')->insert([
                'user_id'=>$uid,
                'consignee'=>$user_address['consignee'], //收货人的姓名,用户页面填写,默认取值表user_address
                'order_sn'=>$orderSn,
                'address'=>$user_address['address'],
                'mobile'=>$user_address['mobile'],
                'email'=>$user_address['email'],
                'district'=>$user_address['district'],
                'is_status'=>2,  //现金支付
                'pay_time'=>time(), //支付时间
                'goods_amount'=>$goods_amount,
            ]);
        //入订单商品信息表
        $order_id = Db::name('order_info')->getLastInsID();
        //查询商品信息
        $goods_info = Db::name('goods')
            ->where('goods_id = '.$gid)
            ->find();
        //查询订单 是否有商品
        $r = Db::name('order_goods')->where('goods_id ='.$gid)->find();
        if($r){
            //修改商品数量
            Db::name('order_goods')
                ->where('goods_id ='.$gid)
                ->update([
                    'order_id'=>$order_id,
                    'goods_number'=>$r['goods_number']+1,
                ]);
        }else{
            Db::name('order_goods')
                ->insert(
                    [
                        'order_id'=>$order_id,
                        'goods_id'=>$gid,
                        'goods_name'=>$goods_info['goods_name'],
                        'goods_sn'=>$goods_info['goods_sn'],
                        'goods_number'=>1,
                        'market_price'=>11,
                        'goods_price'=>21,
                    ]
                );
        }
        //订单详情
        $orderInfo = Db::name('order_goods')
            ->alias('og')
            ->join('order_info oi','oi.order_id=og.order_id')
            ->where('og.order_id ='.$order_id)
            ->find();
        $callback = Request::instance()->get('callback');
        echo $callback.'('.json_encode($orderInfo).')';
    }

    /**
     * 积分夺宝生成订单
     */
    public function orderIneGory()
    {
        $gid = input('get.gid');
        $uid = 1;
        $goods_amount = isset($_GET['goods_amount']) ? $_GET['goods_amount'] : '';
        //查询夺宝表
        $activity = Db::name('goods_activity')
                ->where('goods_id ='.$gid)
                ->find();
        //查询 user_address
        $user_address = Db::name('user_address')
            ->alias('ua')
            ->join('__USERS__ u','u.user_id=ua.user_id')
            ->find();
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        //入订单表
        Db::name('order_info')->insert([
            'user_id'=>$uid,
            'consignee'=>$user_address['consignee'], //收货人的姓名,用户页面填写,默认取值表user_address
            'order_sn'=>$orderSn,
            'address'=>$user_address['address'],
            'mobile'=>$user_address['mobile'],
            'email'=>$user_address['email'],
            'district'=>$user_address['district'],
            'is_status'=>1,  //积分支付
            'pay_time'=>time(), //支付时间
            'goods_amount'=>$goods_amount,
            'inte_'=>$activity['act_integral']
        ]);
        //入订单商品信息表
        $order_id = Db::name('order_info')->getLastInsID();
        //查询商品信息
        $goods_info = Db::name('goods')
            ->where('goods_id = '.$gid)
            ->find();
        //查询订单 是否有商品
        $r = Db::name('order_goods')->where('goods_id ='.$gid)->find();
        if($r){
            //修改商品数量
            Db::name('order_goods')
                ->where('goods_id ='.$gid)
                ->update([
                    'goods_number'=>$r['goods_number']+1,
                ]);
        }else{
            Db::name('order_goods')
                ->insert(
                    [
                        'order_id'=>$order_id,
                        'goods_id'=>$gid,
                        'goods_name'=>$goods_info['goods_name'],
                        'goods_sn'=>$goods_info['goods_sn'],
                        'goods_number'=>1,
                        'market_price'=>11,
                        'goods_price'=>21,
                    ]
                );
        }
        //订单详情
        $orderInfo = Db::name('order_goods')
            ->where('order_id ='.$order_id)
            ->find();
        $callback = Request::instance()->get('callback');
        echo $callback.'('.json_encode($orderInfo).')';
    }


    /**
     * gid 商品id
     * 加入购物车
     */
    public function addCart()
    {
        $gid = input('get.gid');
        $is_status = input('get.is_status');
        $gInfo = Db::name('goods')->where('goods_id ='.$gid)->find();
        $callback = Request::instance()->get('callback');
        $uid = 1;
        if(!$gid){
            $message = ['status'=>0,'message'=>'请先登录'];
        }else{
            $r = Db::name('cart')->where('goods_id ='.$gid)->find();
            if($r){
                //修改商品数量
                Db::name('cart')
                    ->where('goods_id ='.$gid)
                    ->update([
                        'goods_number'=>$r['goods_number']+1,
                    ]);
            }else{
                Db::name('cart')->insert([
                    'user_id'=>$uid,
                    'goods_id'=>$gid,
                    'goods_sn'=>$gInfo['goods_sn'],
                    'goods_name'=>$gInfo['goods_name'],
                    'market_price'=>$gInfo['market_price'],
                    'is_real'=>$gInfo['is_real'],
                    'is_shipping'=>$gInfo['is_shipping'],
                    'is_real'=>$gInfo['is_real'],
                    'goods_number'=>1,
                    'is_status'=>$is_status,
                ]);
            }
            $message = ['status'=>1,'message'=>'加入购物车成功'];
        }

        echo $callback.'('.json_encode($message).')';
    }

    /**
     *购物车数据
     */
    public function Cart_info()
    {
        $uid = 1;
        //查询购物车数据
        $cart_Info = Db::name('cart')
            ->alias('c')
            ->join('goods_activity ga','ga.goods_id = c.goods_id')
            ->join('__GOODS__ g','g.goods_id = c.goods_id')
            ->where('c.user_id ='.$uid)
            ->select();
        $callback = Request::instance()->get('callback');

        echo $callback.'('.json_encode($cart_Info).')';
    }

    /**
     * @author: QGG
     * 现金夺宝
     */
    public function cash_()
    {
        $gid = input('get.gid'); //商品id
        //判断夺宝时间
        $in_detail = Db::name('goods_activity')
            ->alias('ga')
            ->join('__GOODS__ g', 'g.goods_id=ga.goods_id')
            ->where('ga.is_integral = 0 and ga.goods_id ='.$gid)
            ->find();
//        echo Db::getLastSql();die;
//        print_r($in_detail);exit();
        $in_detail['width'] = round($in_detail['act_join']/$in_detail['act_sum'] * 100 ,2)."%";
        $callback = Request::instance()->get('callback');
        if($in_detail){
            $in_detail['status'] = 1;
            echo $callback.'('. json_encode($in_detail). ')';
        }else{
            $in_detail['status'] = 0;
            $in_detail['error_info'] = "数据查询失败";
            echo $callback.'('. json_encode($in_detail). ')';
        }
    }

    /**
     *订单详情
     */
    public function order_Desc()
    {
        $order_id = input("get.order_id");
        $orderInfo = Db::name("order_info")
                    ->alias('oi')
                    ->join('order_goods og','og.order_id=oi.order_id')
                    ->where('oi.order_id ='.$order_id)
                    ->find();
        $callback = input("get.callback");
        if($orderInfo){
            $arr = [
                'status' => 1,
                'content'=>$orderInfo,
            ];
            echo $callback.'('.json_encode($arr).')';
        }else{
            $arr = [
                'status' => 0,
                'content'=>$orderInfo,
            ];
            echo $callback.'('.json_encode($arr).')';
        }


    }

    

}

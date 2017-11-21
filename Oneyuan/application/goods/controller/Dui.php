<?php
namespace app\goods\controller;
use think\Controller;
use app\goods\controller\Common;
use think\Db;
use think\Request;

class Dui extends Common
{
	//查询商品信息
	public function goods_info(){
		//获取商品的id
		$request = Request::instance();
		$goods_id = $request->get('goods_id');
		//通过商品id查询商品的详细信息
		$data=Db::table("ecs_exchange_goods")->join("ecs_goods","ecs_exchange_goods.goods_id=ecs_goods.goods_id")->where("ecs_exchange_goods.goods_id=$goods_id")->select();
		//查询除展示外的其他商品
		$arr = Db::table("ecs_exchange_goods")->join("ecs_goods","ecs_exchange_goods.goods_id=ecs_goods.goods_id")->where("ecs_exchange_goods.goods_id!=$goods_id")->limit(5)->select();
		$user_id = 1;
		//将所有的查询的数据整合到res中发送到前台
		$res['up'] = $data;//详情
		$res['down'] = $arr;//商品列表
		$res['user_id'] = $user_id;//用户id
		$this->get_msg("200","成功",$res);
	}
	//判断用户的积分以及商品库存量是否能兑换
	public function is_full(){
		//获取商品的id和用户的id
		$request = Request::instance();
		$param = $request->get();
		//通过用户的id查询用户的信息
		$user = Db::table("ecs_users")->where("user_id=".$param['user_id'])->find();
		//通过商品的id查询商品的信息
		$goods = Db::table("ecs_exchange_goods")->where("goods_id=".$param['goods_id'])->find();
		//如果用户拥有的积分大于兑换商品的积分才能兑换，否则就提示用户积分不够，返回展示兑换商品页
		$goodsinfo = Db::table("ecs_goods")->field("goods_number")->where("goods_id=".$param['goods_id'])->find();
		if($user['user_integral']<$goods['exchange_integral']){
			$res['error'] = 0;
			$res['content'] = "积分不够";
		}else if($goodsinfo['goods_number']<=0){
			$res['error'] = 0;
			$res['content'] = "库存数量不够";
		}else{
			$res['error'] = 1;
		}
		$this->get_msg("200","成功",$res);
	}
	//兑换换商品信息
	public function dui_goods_info(){
		//获得商品和用户的ID
		$request = Request::instance();
		$goods_id = $request->get('goods_id');
		$user_id = $request->get("user_id");
		//查询兑换商品的详细信息（商品名称、需要积分等等）
		$data=Db::table("ecs_exchange_goods")->join("ecs_goods","ecs_exchange_goods.goods_id=ecs_goods.goods_id")->where("ecs_exchange_goods.goods_id=$goods_id")->find();
		//通过用户id查询用户的信息
		$data2 = Db::table("ecs_users")->where("user_id=".$user_id)->find();
		//使用用户信息中地址的id查地址的信息
		$data1 = Db::table("ecs_user_address")->where("address_id=".$data2['address_id'])->select();
		//将用户的相信地址拼接起来成为字符串
		foreach ($data1 as $key => $value) {	
			$province = Db::name('region')->field("region_name")->where("region_id=".$value['province'])->find();
			$city = Db::name("region")->field("region_name")->where("region_id=".$value['city'])->find();
			$district = Db::name("region")->field("region_name")->where("region_id=".$value['district'])->find();
			$address[] = $province['region_name'].$city['region_name'].$district['region_name'].$value['address'];
		}
		//查询所有的快递方式展示在前台页面中
		$shipping = Db::table('ecs_shipping')->select();
		//将查询到的用户、商品、快递的信息发送到前台
		$res['info'] = $data;
		$res['address'] = $address;
		$res['shipping'] = $shipping;
		$this->get_msg("200","成功",$res);
	}
	//兑换商品加入订单表
	public function add_orderinfo(){
		//接收前台的参数
		$request = Request::instance();
		$param = $request->get();
		//通过用户的ID查询用户的相信信息
		$userinfo = Db::table("ecs_user_address")->where("user_id=".$param['user_id'])->find();
		//订单编号
		$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
		$order['order_sn'] = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

		//用户id
		$order['user_id'] = $param['user_id'];

		//订单状态
		$order['order_status']=1;
		//商品配送状态
		$order['shipping_status']=0;
		//支付状态
		$order['pay_status'] = 2;
		//收货人姓名
		$order['consignee'] = $userinfo['consignee'];
		//国家
		$order['country'] = $userinfo['country'];

		//省份
		$order['province'] = $userinfo['province'];
		//城市
		$order['city'] = $userinfo['city'];
		//地区
		$order['district'] = $userinfo['district'];
		//详细地址
		$order['address'] = $userinfo['address'];
		//邮编
		$order['zipcode'] = $userinfo['zipcode'];
		//电话
		$order['tel'] = $userinfo['tel'];
		//手机
		$order['mobile'] = $userinfo['mobile'];
		//邮箱
		$order['email'] = $userinfo['email'];
		//最佳配送时间
		$order['best_time'] = $userinfo['best_time'];

		//标志建筑物
		$order['sign_building'] = $userinfo['sign_building'];
		//订单附言
		$order['postscript'] = '无';
		//快递方式id
		$order['shipping_id'] = $param['shipping_id'];
		//快递方式
		$shippinginfo = Db::table("ecs_shipping")->where("shipping_id=".$param['shipping_id'])->find();
		$order['shipping_name'] = $shippinginfo['shipping_name'];

		//支付id
		$order['pay_id'] = '';
		//支付方式
		$order['pay_name'] = '';
		//缺货处理方式
		$order['how_oos'] = '';
		//余额处理方式
		$order['how_surplus'] = '';
		//包装名称
		$order['pack_name'] = '';
		//贺卡名称
		$order['card_name'] = '';
		//贺卡内容
		$order['card_message'] = '';
		//发票抬头
		$order['inv_payee'] = '';
		//发票内容
		$order['inv_content'] = '';
		//商品总额
		$goodsinfo = Db::table("ecs_goods")->where("goods_id=".$param['goods_id'])->find();
		$order['goods_amount'] = $goodsinfo['shop_price'];
		//配送费用
		$order['shipping_fee'] = 0;
		//保价费用
		if(strpos($shippinginfo['insure'],'%')){
			$order['insure_fee'] = ($goodsinfo['shop_price']*$shippinginfo['insure']);
		}else{
			$order['insure_fee'] = $shippinginfo['insure'];
		}
		//支付费用
		$order['pay_fee'] = 0;
		//包装费用
		$order['pack_fee'] = 0;
		//贺卡费用
		$order['card_fee'] = 0;
		//商品优惠总额
		$order['goods_discount_fee'] = 0;
		//已付金额
		$order['money_paid'] = $goodsinfo['shop_price'];
		//订单使用余额的数量
		$order['surplus'] = 0;
		//使用积分的数量
		$jifeninfo = Db::table("ecs_exchange_goods")->where("goods_id=".$param['goods_id'])->find();
		$order['integral'] =$jifeninfo['exchange_integral'];

		//使用积分金额
		$order['integral_money'] = $goodsinfo['shop_price'];
  		//使用红包金额
  		$order['bonus'] = 0;
  		//应付款金额
  		$order['order_amount'] = $goodsinfo['shop_price'];
  		//订单由某广告带来的广告ID
  		$order['from_ad'] = 0;
  		//订单的来源页面
  		$order['referer'] = '';
  		//订单生成时间
  		$order['add_time'] = time();
		//订单确认时间
		$order['confirm_time'] = time();
 		//订单支付时间
 		$order['pay_time'] = time();
		//订单配送时间
		$order['shipping_time'] = '';
		//包装ID
		$order['pack_id'] = 0;
 		//贺卡ID
 		$order['card_id'] = 0;
 		//红包ID
 		$order['bonus_id'] = 0;
 		//发货时填写
 		$order['invoice_no'] = '';
		//通过活动购买的商品的代号,group_buy是团购; auction是拍卖;snatch夺宝奇兵;正常普通产品该处理为空
		$order['extension_code'] = '';
		//通过活动购买的物品id,取值ecs_good_activity;如果是正常普通商品,该处为0 
		$actinfo = Db::table("ecs_goods_activity")->where("goods_id=".$param['goods_id'])->find();
		$order['extension_id'] = $actinfo['act_id'];
		//商家给客户的留言,当该字段值时可以在订单查询看到 
		$order['to_buyer'] = '';
		//付款备注, 在订单管理编辑修改
		$order['pay_note'] = '';
 		//该笔订单被指派给的办事处的id, 根据订单内容和办事处负责范围自动决定,也可以有管理员修改,取值于表agency 
 		$order['agency_id'] = 0;
		//发票类型,用户页面选择,取值shop_config的code字段的值invoice_type的value 
		$order['inv_type'] = '';
 		//发票税额
  	 	$order['tax'] = 0;
 		//0未分成或等待分成;1已分成;2取消分成
 		$order['is_separate'] = 0;
 		//自增ID 
 		$order['parent_id'] = 0;
	  	//订单号,唯一 
	  	$order['discount'] = 0;
  		//
  		$order['callback_status'] = 'true';
		//
		$order['lastmodify'] = '';	
  		//选择订单的模块，比如积分商城就是1
  		$order['is_status'] = 1;
  		//将兑换商品的信息加入到订单表中
  		$res = Db::table("ecs_order_info")->insert($order);
  		//查询收货人的信息
  		$user = Db::table("ecs_users")->where("user_id=".$param['user_id'])->find();
  		//查询兑换商品的积分
  		$data=Db::table("ecs_exchange_goods")->where("goods_id=".$param['goods_id'])->find();
  		//修改用户的积分=用户的积分-兑换商品的积分
		$res1 = Db::table("ecs_users")->where("user_id=".$param['user_id'])->update(array("user_integral"=>($user['user_integral']-$data['exchange_integral'])));
		$res4 = Db::table("ecs_goods")->where("goods_id=".$param['goods_id'])->find();
		$res5 = Db::table("ecs_goods")->where("goods_id=".$param['goods_id'])->update(array("goods_number"=>($res4['goods_number']-1),"exchange_number"=>($res4['exchange_number']+1)));
		//将用户的操作记录添加到account_log表中
		$res2['user_id'] = $param['user_id'];
		$res2['pay_points'] = "-".$data['exchange_integral'];
		$res2['change_time'] = time();
		$res2['change_desc'] = "兑换了".$goodsinfo['goods_name'];
		$res2['change_type'] = 99;
		$res3 = Db::table("ecs_account_log")->insert($res2);
		if($res&&$res3&&$res5){
			$result['error']=1;
			$result['content'] = "兑换成功";
		}else{
			$result['error']=0;
			$result['content'] = "兑换失败";
		}
  	 	$this->get_msg("200","成功",$result);
	}

	public function sel_orderinfo(){
		$request = Request::instance();
		$param = $request->get();
		//查询收货人的信息
  		$user = Db::table("ecs_users")->where("user_id=".$param['user_id'])->find();
  		//查询商品的信息
  		$goodsinfo = Db::table("ecs_goods")->where("goods_id=".$param['goods_id'])->find();
  		//查询兑换商品的积分
  		$data=Db::table("ecs_exchange_goods")->where("goods_id=".$param['goods_id'])->find();
		//地址拼接
  		$data1 = Db::table("ecs_user_address")->where("address_id=".$user['address_id'])->select();
		foreach ($data1 as $key => $value) {	
			$province = Db::name('region')->field("region_name")->where("region_id=".$value['province'])->find();
			$city = Db::name("region")->field("region_name")->where("region_id=".$value['city'])->find();
			$district = Db::name("region")->field("region_name")->where("region_id=".$value['district'])->find();
			$address[] = $province['region_name'].$city['region_name'].$district['region_name'].$value['address'];
		}
		//用户兑换完商品以后查询用户现拥有的积分
		$now = Db::table("ecs_users")->where("user_id=".$param['user_id'])->find();
		//兑换完成将兑换信息展示在页面当中
		$result['address'] = $address[0];
		$result['now'] = $now['user_integral'];
		$result['exchange_integral'] = $data['exchange_integral'];
		$result['goods_name'] = $goodsinfo['goods_name'];
		$this->get_msg("200","成功",$result);
	}
	
}

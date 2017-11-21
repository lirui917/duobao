<?php 
namespace app\goods\controller;


use think\Request;
use think\Controller;
use app\goods\controller\Common;
use think\Db;
/**
 * @Author:      I'm  麻辣烫
 * @Email:       999999999@qq.com
 * @DateTime:    2017-11-10 16:56:49
 * @Description: 夺宝接口 
 */


class Hotactivity extends Controller
{
	
	//夺宝热门展示



	public function hot_snatch()
	{	
		$m = new Common();
		//查询兑换表的展示
		$data = Db::table('ecs_goods_activity')->join("ecs_goods","ecs_goods_activity.goods_id=ecs_goods.goods_id")->where("ecs_goods_activity.is_hot=1")->select();

		if($data){
			$m->get_msg('200','ok',$data);
		} else {
			$m->get_msg('201','no');

		}
	}
	//夺宝幸运码
	public function snatch_lucky()
	{
		$activity = Db::table("ecs_goods_activity")->field('end_time,act_join')->select();
		$now = time();

	    foreach($activity as $k=>$v){

	    	if($now-$v['end_time'] > 60*60){
				$arr[] = ['end_time'=>$v['end_time'],'join'=>$v['act_join']];	    	   
	    	}
	    }

	 	//计算结果
	    // $buying = Db::table("ecs_activity_user")->field("act_buying")->order("act_buying","desc")->limit(100)->select();
	    // $buying = array_column($buying,'act_buying');
	 
	    // foreach($buying as $k=>$v){
	    // 	$buy[]=strtotime(substr($v,strpos($v, ' ')+1));
	    // }
	    // $sum = array_sum($buy);
	   	 $buying = Db::table("ecs_activity_user")->field("act_buying")->order("act_buying","desc")->limit(100)->select();
	     $buying = array_column($buying,'act_buying');
	 
	     foreach($buying as $kk=>$vv){
	     	$buy[]=substr($vv,strpos($vv, ' ')+1);

	     }
	      
	     $buys = implode(',', $buy);
		 $buys = str_replace(':', '', $buys);
		 $buys = str_replace('.', '', $buys);
		 $sum = explode(',', $buys);

	     $sum = array_sum($sum);
	    foreach($arr as $k=>$v){

         //幸运码
	     // $t = substr(time(),-3);
	     // $l = rand(100,999);
	     // $d = 1000;
	     // $lucky = $d.$t.$l;
	    //计算结果


	     
	     $lucky = $sum%$v['join'];
	       
	     $lucky = 10000001+$lucky;
	     
	   	 
	    $time = date('Y-m-d H:i').':'.date('s.B'); 
	   	  

	   	 $res = Db::table('ecs_goods_activity')->where(['end_time'=>$v['end_time']])->update(['lottery'=>$lucky,'make_time'=>$time]);
	   	 sleep(2);
	    }
		
	}

	//最新揭晓
	public function new_announce()
	{

	    $m = new Common();

		//展示最新揭晓
		$data = Db::table('ecs_activity_user')->alias('u')->field("a.lottery,a.lottery,a.make_time,a.act_join,s.user_name,a.goods_id,a.act_id")->join("ecs_goods_activity a",'u.act_id=a.act_id')->join("ecs_users s","u.user_id=s.user_id")->select();
		$goods_id = array_column($data, 'goods_id');
	   
	    $img = Db::table("ecs_goods")->field("goods_img")->where(['goods_id'=>['in',$goods_id]])->select();

	    foreach($img as $k=>$v){
	    	$data[$k]['goods_img'] = $v['goods_img'];
	    }

		// $act_id = array_column($user_id, 'act_id');
		 
		// 找到夺宝字段
		// $activity = Db::table('ecs_goods_activity')->where(['act_id'=>['in',$act_id]])->select();
		if($data){

			$m->get_msg('200','ok',$data);

		} else {
			$m->get_msg('201','no');

		}
		
	}
	//最新揭晓 结果
	public function announce_info()
	{
		//这块有点bug
		$m = new Common();
		$get = input('get.');
		$act_id = $get['act_id'];
		$user_id = $get['user_id'];
		//揭晓结果展示
		$activity = Db::table("ecs_goods_activity")->alias("a")->join("ecs_activity_user u","a.act_id=u.act_id")->where("u.act_id=$act_id")->select();

		//查到商品id
		$goods_id = array_column($activity,'goods_id');

		//查询商品表
		$goods = Db::table("ecs_goods")->where(['goods_id'=>['in',$goods_id]])->select();
		
		//查询用户名
		$user = Db::table("ecs_users")->field("user_name")->where("user_id=$user_id")->find();
		//查询其他正在进行的
		$date = Db::table('ecs_goods_activity')->select();
		$make = array_column($date, "make_time");	

		$doing = '';
		foreach($date as $kk=>$vv){
			 if(in_array('',$make)){
	        
				 $doing = $vv['act_priod'];
			}
		}
	  
		foreach($goods as $k=>$v){
		
			$activity[$k]["doing"] = $doing;
			
			//揭晓时间
			$time = explode(' ', $activity[$k]['make_time']);
			$activity[$k]['make_qian'] = $time[0];
			$activity[$k]['make_hou'] = $time[1];

			//云购时间
			$buying = explode(' ', $activity[$k]['act_buying']);
			$activity[$k]['buying_qian'] = $buying[0];
			$activity[$k]['buying_hou'] =  $buying[1];

			$activity[$k]['goods_img'] = $v['goods_img'];
			$activity[$k]['goods_name'] = $v['goods_name'];
			$activity[$k]['shop_price'] = $v['shop_price'];
			$activity[$k]['user_name'] = $user['user_name'];

		}
	 
		 
		if(is_array($activity)){

 			$m->get_msg('200','ok',$activity);

		} else {

 			$m->get_msg('201','no');

		}
	}
	//兑换热门展示 
	public function hot_goods()
	{
		$m = new Common();
		$data = Db::table('ecs_exchange_goods')->join("ecs_goods","ecs_exchange_goods.goods_id=ecs_goods.goods_id")->where("ecs_exchange_goods.is_hot=1")->limit(3)->select();
	 
		if($data){
			$m->get_msg('200','ok',$data);
		} else {
			$m->get_msg('201','no');

		}
	}


	//首页数据展示1元夺购
	public function one_duogou()
	{
		   $m = new Common();
	       $one = Db::table('ecs_goods_activity')->alias("a")->join("ecs_goods g","a.goods_id=g.goods_id")->field("a.start_time,a.end_time,g.goods_img,g.market_price,a.act_id,a.act_name")->where("one_duogou=1")->select();
	       foreach($one as $k=>$v){
	       		$one['time'] = time();
	       }
	      // var_dump($one);die;
	       if($one){
	       	 $m->get_msg('200','ok',$one);
	       } else {
	       	 $m->get_msg('201','no');
	       }
	       
	}
	//首页的一元夺宝详情
	public function index_duogou_info()
	{
	     $m = new Common();
		  
         $act_id = input('get.act_id');
         $act = Db::table("ecs_goods_activity")->alias("a")->join("ecs_goods g","a.goods_id=g.goods_id")->field("a.act_join,a.act_sum,g.market_price,g.goods_img,a.act_name,a.act_id")->where("a.act_id=$act_id")->find();
       	 $act['bfb'] = round($act['act_join']/$act['act_sum']*100,2)."%";
       	  
         if($act){
         	 
         	$m->get_msg('200','ok',$act);

         } else {
         	$m->get_msg('201','no');

         }
	}
	//首页一元夺宝添加购物车
	public function add_gwc()
	{
		$user_id = input('get.user_id');
		$act_id = input('get.act_id');

		$data = Db::query("select * from ecs_goods where goods_id = (select goods_id from ecs_goods_activity where act_id=$act_id)");
		//var_dump($data);die;
		$add['user_id'] = $user_id;
		$add['goods_id'] = $data[0]['goods_id'];
		$add['goods_sn'] = $data[0]['goods_sn']; 
		$add['product_id'] = 0;
		$add['goods_name'] = $data[0]['goods_name'];
		$add['market_price'] = $data[0]['market_price'];
		 
		$add['goods_number'] = $data[0]['goods_number'];
		 
		$add['rec_type'] = 2;
		$add['is_real'] = $data[0]['is_real'];
		$add['parent_id'] = 0;

		$add['extension_code'] = $data[0]['extension_code'];
		$add['goods_price'] = $data[0]['shop_price'];

	    

		//  rec_id 	mediumint(8)  	否 	自增id号
		// user_id 	mediumint(8)  	否 	用户登录ID;取自session
		// session_id 	char(32) 	否 	如果该用户退出,该Session_id对应的购物车中所有记录都将被删除
		// goods_id 	mediumint(8)  	否 	商品的ID,取自表goods的goods_id
		// goods_sn 	varchar(60) 	否 	商品的货号,取自表goods的goods_sn
		// product_id 	mediumint(8) 	否 	 
		// goods_name 	varchar(120) 	否 	商品名称,取自表goods的goods_name
		// market_price 	decimal(10,2)  	否 	商品的本店价,取自表市场价
		// goods_price 	decimal(10,2) 	否 	商品的本店价,取自表goods的shop_price
		// goods_number 	smallint(5)  	否 	商品的购买数量,在购物车时,实际库存不减少
		// goods_attr 	text 	否 	商品的扩展属性, 取自goods的extension_code
		// is_real 	tinyint(1)  	否 	取自ecs_goods的is_real
		// extension_code 	varchar(30) 	否 	商品的扩展属性,取自goods的extension_code
		// parent_id 	mediumint(8)  	否 	该商品的父商品ID,没有该值为0,有的话那该商品就是该id的配件
		// rec_type 	tinyint(1)  	否 	购物车商品类型;0普通;1团够;2拍卖;3夺宝奇兵
		// is_gift 	smallint(5)  	否 	是否赠品,0否;其他, 是参加优惠活动的id,取值于favourable_activity的act_id
		// is_shipping 	tinyint(1) 	否 	 
		// can_handsel 	tinyint(3)  	否 	能否处理
		// goods_attr_id 	mediumint(8) 	否 	该商品的属性的id,取自goods_attr的goods_attr_id,如果有多个,只记录了最后一个,可能是bug
		Db::table("ecs_cart")->insert($add);
		
		// $result = Db::table('banner_item')
  //           ->where(function ($query) use($id){
  //               $query->where('banner_id','=',$id);

  //           })
  //           ->select();
 
	}
	//首页购物车展示
	public function gwc_show()
	{
		 $m = new Common();
		 $cart = Db::table("ecs_cart")->alias("c")->join("ecs_goods_activity a","c.goods_id=a.goods_id","inner")->join("ecs_goods s","s.goods_id=c.goods_id","inner")->field("s.goods_name,s.goods_img,a.act_join,a.act_sum,c.rec_id")->where("user_id=1")->select();
		 
		 if($cart){
		 	$m->get_msg('200','ok',$cart);
		 } else {
		 	$m->get_msg('201','no');
		 }
	   
	}
	
		//查询所有的配送的方式
	public function shipping()
	{
		$m = new Common();
		//当前的用户id
		$uid = input('get.uid');
		//接收购物车的id
		$cart_id = input('get.cart_id');
		//物流方式
			$shipping = Db::table("ecs_shipping")->select();
			//支付方式
		$pay = Db::query("select pay_name,pay_id,pay_desc from ecs_payment where pay_id=5 or pay_id=6");
		//两表联查 返回用户所在的国家及 详细地址，姓名，手机号
		$country =Db::query("select ecs_region.region_id,ecs_region.region_name,ecs_user_address.consignee,ecs_user_address.address,ecs_user_address.mobile  from ecs_users inner join ecs_user_address on ecs_users.address_id=ecs_user_address.address_id inner join ecs_region on ecs_user_address.country=ecs_region.region_id where ecs_users.user_id=$uid");
		//两表联查 返回用户所在的省
		$province  = Db::query("select ecs_region.region_id,ecs_region.region_name from ecs_users inner join ecs_user_address on ecs_users.address_id=ecs_user_address.address_id inner join ecs_region on ecs_user_address.province = ecs_region.region_id where ecs_users.user_id=$uid");
		
	    //两表联查 返回用户所在的市 
	    $city  = Db::query("select ecs_region.region_id,ecs_region.region_name from ecs_users inner join ecs_user_address on ecs_users.address_id=ecs_user_address.address_id inner join ecs_region on ecs_user_address.city = ecs_region.region_id where ecs_users.user_id=$uid");
	   	
	    //两表联查 返回用户所在的县
		$district  = Db::query("select ecs_region.region_id,ecs_region.region_name from ecs_users inner join ecs_user_address on ecs_users.address_id=ecs_user_address.address_id inner join ecs_region on ecs_user_address.district = ecs_region.region_id where ecs_users.user_id=$uid");
		$user_name = $country[0]["consignee"].".".$country[0]["mobile"]."☺";
		$address = $country[0]["region_name"]." ".$province[0]["region_name"]." ".$city[0]["region_name"]." ".$district[0]["region_name"]."☆".$country[0]["address"];

		$address_id = $country[0]["region_id"].",".$province[0]["region_id"].",".$city[0]["region_id"].",".$district[0]["region_id"];
		//查看商品信息
		$cart = Db::query("select ecs_cart.rec_id,ecs_cart.goods_attr_id,ecs_cart.goods_id,ecs_cart.goods_name,ecs_cart.goods_price,ecs_cart.market_price,ecs_cart.goods_number,ecs_goods_attr.goods_attr_name from ecs_cart inner join ecs_goods_attr on ecs_cart.goods_attr_id=ecs_goods_attr.goods_attr_id where ecs_cart.rec_id in ($cart_id)");
		  $data['user_name']= $user_name;
		  $data['address'] = $address;
		  $data['shipping']=$shipping;
		  $data['address_id'] = $address_id;
		  $data['pay']=$pay;
		  $data['cart'] = $cart;

		  $m->get_msg("200",'ok',$data);
	}
	//添加购物车
	public function add_gwc_do()
	{
		$get = input('get.');
		//用户id
		$uid = 3;
		$level = $this->invitation($uid);
		 
		//一级用户
		if($level['first']){

			foreach($level['first'] as $k=>$v){

				$arr[] = ['b_id'=>$uid,'y_id'=>$v['user_id'],'desc'=>'一元夺购','status'=>1,"yongjin"=>(1*0.05),'addtime'=>time()]; 
				Db::table("ecs_admin_invited")->where('user_id='.$v['user_id'].' and beinviter_id='.$uid)->update(['status'=>1]);
			}
			//二级用户
			if($level['two']){
				foreach($level['two'] as $k=>$v){
				$arr[] = ['b_id'=>$uid,'y_id'=>$v['user_id'],'desc'=>'一元夺购','status'=>1,"yongjin"=>(1*0.02),'addtime'=>time()]; 
				Db::table("ecs_admin_invited")->where('user_id='.$v['user_id'].' and beinviter_id='.$uid)->update(['status'=>1]);
				}
				//三级用户
				if($level['three']){
					foreach($level['three'] as $k=>$v){
					$arr[] = ['b_id'=>$uid,'y_id'=>$v['user_id'],'desc'=>'一元夺购','status'=>1,"yongjin"=>(1*0.01),'addtime'=>time()]; 
					Db::table("ecs_admin_invited")->where('user_id='.$v['user_id'].' and beinviter_id='.$uid)->update(['status'=>1]);
					}
				}
			}
		}
	 	
		foreach($arr as $K=>$v){
			$res = Db::table("ecs_commission")->insert($arr[$K]);
			 
		}

		$address_id = explode(",",$get['address_id']);
		$country = $address_id[0] ? $address_id[0] : '';
		$province = $address_id[1] ? $address_id[1] :'';
		$city = $address_id[2] ? $address_id[2] : '';
		$district = $address_id[3] ? $address_id[3] : '';
		
		$consignee = substr($get['address'],0,strpos($get['address'],'.'));
		$param['order_sn'] = date('YmdHis').time();
		$param['user_id'] = $get['uid'];
		$param['order_status']=0;
		$param['shipping_status']=0;
		$param['pay_status']=0;
		
		$param['consignee']=$consignee;
		$param['country']=$country;
		$param['province']=$province;
		$param['city']=$city;
		$param['district']=$district;
		$param['address']=substr($get['address'],strpos($get['address'],'☆')+3);
		$param['mobile']=substr($get['address'],strpos($get['address'],'.')+1,strpos($get['address'],'☺')-7);

		$param['postscript']=$get['order_desc'];
		$param['shipping_id']=$get['shipping_id'];
		$param['shipping_name']=$get['shipping_name'];
		$param['pay_id']=$get['pay_id'];
		$param['pay_name']=$get['pay_name'];
		$param['goods_amount']=$get['sum_price'];
		$param['add_time']=time();
		$res = Db::table("ecs_order_info")->insert($param);
		$order_id = Db::table('ecs_order_info')->getLastInsID();
		$cart_id = explode(",",$get['cart_id']);
		$send_number =0;
			foreach($cart_id as $v){
				$cart_show = Db::table("ecs_cart")->field(['goods_name','goods_id','goods_number','goods_attr_id','market_price','goods_price','goods_sn'])->where("rec_id",$v)->find();
				$cart_show['send_number']=$send_number;
				$cart_show['order_id']=$order_id;
				Db::table("ecs_order_goods")->insert($cart_show);
			}


	}
 	public function invitation($b_id)
 	{
 		$data = [];
 		$data['first'] = isset($data['first']) ? $data['first'] : '';
 		$data['two'] = isset($data['two']) ? $data['two'] : '';
 		$data['three'] = isset($data['three']) ? $data['three'] : '';

 		$data['first'] = Db::table("ecs_admin_invited")->where("beinviter_id=$b_id")->select();
 		if($data['first']){
 			foreach($data['first'] as $k=>$v){
 			//二级
	 		$er = Db::table("ecs_admin_invited")->where("beinviter_id=".$v['user_id'])->select();
	 			if($er){
	 				$data['two'] = $er;
	 			}
	 		}
 		}
 		if($data['two']){
 			foreach($data['two'] as $k=>$v){
			//三级
			$san = Db::table("ecs_admin_invited")->where("beinviter_id=".$v['user_id'])->select();
				if($san){

					$data['three'] = $san;
				}
			}
 		}
		
		
		
		return $data;

 	}
 	 // public function invited_list(){
        
   //      $user_id=isset($_GET['user_id'])?$_GET['user_id']:'';
   //      if($user_id==''){
   //         $this->get_msg('104','缺少参数');die;
   //      }
   //      $data['count'] = 0;
   //      $data['number']['first']['total']=$data['number']['first']['is_pay']=0;
   //      $data['number']['second']['total']=$data['number']['second']['is_pay']=0;
   //      $data['number']['third']['total']=$data['number']['third']['is_pay']=0;        
   //      //一级用户
   //      $data['first']=Db::name('admin_invited')->alias('v')->join('admin_user u','v.beinviter_id=u.user_id')->field('v.invited_id,v.user_id,v.beinviter_id,v.addtime,v.status,u.user_name')->where('v.user_id',$user_id)->select();
   //      if($data['first']){
   //          $data['number']['first']=$this->getNum($data['first']);
   //          foreach ($data['first'] as $key => $val) {
   //              //二级用户
   //              $info=Db::name('admin_invited')->alias('v')->join('admin_user u','v.beinviter_id=u.user_id')->field('v.invited_id,v.user_id,v.beinviter_id,v.addtime,v.status,u.user_name')->where('v.user_id',$val['beinviter_id'])->select();
   //              if($info){
   //                  $data['second'][]=$info;  
   //              }
   //          }
   //      }

   //      if($data['second']){         
   //          //三级用户
   //          foreach ($data['second'] as $k => $v) {
   //              $arr=$this->getNum($v);
   //              $data['count']+=$arr['count'];
   //              $data['number']['second']['total']+=$arr['total'];
   //              $data['number']['second']['is_pay']+=$arr['is_pay'];
   //              foreach ($v as $key => $val) {
   //                  $res=Db::name('admin_invited')->alias('v')->join('admin_user u','v.beinviter_id=u.user_id')->field('v.invited_id,v.user_id,v.beinviter_id,v.addtime,v.status,u.user_name')->where('v.user_id',$val['beinviter_id'])->select();
   //                  if($res){
   //                      $data['third'][]=$res;
   //                  }
   //              }
   //          }
   //      }
   //      if($data['third']){
   //          foreach ($data['third'] as $k => $v) {
   //              $arr=$this->getNum($v);
                
   //              $data['number']['third']['total']+=$arr['total'];
   //              $data['number']['third']['is_pay']+=$arr['is_pay'];
   //          }
   //      }
   //      // var_dump($data);die;
   //      $this->get_msg("100",$data);
   //  }
 		// }
	 
}



 ?>
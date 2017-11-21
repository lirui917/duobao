<?php 
	namespace app\goods\controller;
	use think\Controller;
	use think\Db; 
	use think\Request;
	use app\goods\controller\Common;
	class Jifen extends Common
	{
		public function mall()
		{
			$data = Db::name('goods')->field('goods_name,goods_id,goods_thumb,market_price,goods_number,give_integral')->select();
			if($data)
			{
				$this->get_msg("200","成功",$data);
			}
		}
		public function duogou()
		{
			$user = Db::query("select * from ecs_users");
			foreach ($user as $k => $v)
			{
				$res = Db::query("select count(user_id),max(act_buying) from ecs_activity_user where user_id=".$v['user_id']);
				$user[$k]['sum'] = $res[0]['count(user_id)'];
				$user[$k]['act_buying'] = $res[0]['max(act_buying)'];
			}
			if($user)
			{
				$this->get_msg("200","成功",$user);
			}
		}
		public function grzy()
		{
			$request = Request::instance();
			$id = $request->get('id');
			$user = Db::name('users')->field('user_name')->where('user_id',$id)->find();//用户信息
			$act_id = Db::name('activity_user')->field('act_id')->where('user_id',$id)->select();
			foreach ($act_id as $k => $v)
			{
				$res=Db::name('goods_activity')->where('act_id',$v['act_id'])->find();
				$res1 = Db::name('goods')->field('goods_img')->where('goods_id',$res['goods_id'])->find();
				$res['goods_img']=$res1['goods_img'];
				$arr[] = $res;
			}
			$response['user'] = $user;
			$response['arr'] = $arr;
			if($response)
			{
				$this->get_msg("200","成功",$response);
			}
		}
		//获得商品
		public function get_goods()
		{
			$request = Request::instance();
			$id = $request->get('id');
			$user = Db::name('activity_user')->where('user_id',$id)->select();
			 
			foreach ($user as $k => $v)
			{
				$user1 = Db::name('goods_activity')->where('act_id',$v['act_id'])->select();

				foreach ($user1 as $key => $val)
				{
					if($v['act_lucky']==$val['lottery']){
						$res1 = Db::name('goods')->field('goods_img,shop_price')->where('goods_id',$val['goods_id'])->find();
						$user1[$key]['goods_img'] = $res1['goods_img'];
						$user1[$key]['shop_price'] = $res1['shop_price'];
						 
					}
				}
			}
		    
			if($user1)
			{
				$this->get_msg("200","成功",$user1);
			}
		}
		public function shaidan()
		{
			$request = Request::instance();
			$id = $request->get('id');
			$user = Db::name('users_activity')->where('user_id',$id)->where('is_sd',1)->select();

			foreach ($user as $k => $v) {
				$time=time()-$v['ad_time'];
				if($time<=600)
				{
					$user[$k]['ad_time']="刚刚";
				}
				else if($time>600&&$time<=3600)
				{
					$user[$k]['ad_time']=floor($time/60).'分钟前';
				}
				else
				{
					$user[$k]['ad_time']=date('Y-m-d H:i:s',$v['ad_time']);
				}
			}
			if($user)
			{
				$this->get_msg("200","成功",$user);
			}
		}
		public function shaidan_detail()
		{
			$request = Request::instance();
			$id = $request->get('id');
			$data=Db::name('users_activity')->join('users','ecs_users_activity.user_id=users.user_id')->join('goods_activity','ecs_users_activity.act_id=goods_activity.act_id')->join('goods','goods_activity.goods_id=goods.goods_id')->field('make_time,ad_time,lottery,act_join,user_name,user_image,sd_title,sd_img,sd_content,goods_img,goods.goods_name,shop_price,goods_activity.act_id')->where('user_act_id',$id)->find();
				$time=time()-$data['ad_time'];
			if($time<=600)
			{
				$data['ad_time']="刚刚";
			}
			else if($time>600&&$time<=3600)
			{
				$data['ad_time']=floor($time/60).'分钟前';
			}
			else
			{
				$data['ad_time']=date('Y-m-d H:i:s',$data['ad_time']);
			}
			if($data)
			{
				$this->get_msg("200","成功",$data);
			}
		}
	}
 ?>
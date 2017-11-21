<?php
/**
 * @author ***** 
 * @param [type] store  [description]: 积分商城主页    
 * @param [type] review [description]: 往期回顾
 * @param [type] imagetext [description]: 图文详情
 */
namespace app\goods\Controller;

use think\Db;
use think\Controller;
use think\Request;
use app\goods\Controller\Common;  //继承控制器


class Biaoge extends Common
{   
    /**
     * [store 积分商城--进入页接口]
     * @return [type] json [description]:积分商城主页数据
     */
	public function store() {

		  $data = Db::name('goods')->field('goods_id,shop_price,goods_number,exchange_number,goods_name,integral,goods_img')->limit(0,130)->select(); 

          $info = self::get_msgs('success','数据返回成功',$data);

          $list = json_encode($info);

          return $info;

        
	}
	/**
	 * [review description] 往期回顾
	 * @param  integer $id   [description] 分页id
	 * @return [type]  json  [description] 回顾商家
	 */
	public function review($id=0) {

		 $limit = 10;            //获取多少条
          $begin = $id*$limit+1;  //开始条数  

          $data = Db::query('SELECT ecs_goods_activity.act_id,ecs_users.user_name,ecs_goods_activity.act_sum,ecs_goods_activity.act_priod,ecs_goods_activity.make_time,ecs_activity_user.act_buying FROM
     ecs_goods_activity
INNER JOIN ecs_activity_user ON ecs_activity_user.act_id = ecs_goods_activity.act_id
INNER JOIN ecs_users ON ecs_users.user_id = ecs_activity_user.act_id');
           
           // var_dump($data);die;
          if(empty($data)){
               $info = self::get_msgs('error','已经没有数据',$data);
          }else{
               $info = self::get_msgs('success','数据获取成功',$data);
          }
          return $info;
	}




   /**
   * [teletext description]  图文详情方法
   * @param  integer $id [description]  图文详情id 
   * @return [type]  json   [description]  json数据
   */
	public function imagetext($id=2) {
		 $data = Db::name('goods')->where('goods_id',$id)->field('goods_id,goods_desc,goods_img')->select();  //商品数据
     if(empty($data))   $info = self::get_msgs('error','该商品已下架，请联系客服人员',$data);
        
     else  $info = self::get_msgs('success','商品获取成功',$data);
     
     return $info; 

	}




	/**
	* [get_msg description]   转换json数据
	* @param  [type] $code    [description]  信息状态
	* @param  [type] $message [description]  状态信息
	* @param  [type] $data    [description]  数据
	* @return [type]          [description]  json字段串
	*/
	public static function get_msgs($code,$message,$data) {
	 
		 $callback = isset($_GET['callback'])?$_GET['callback']:'';

			 if(isset($callback)) {
			 	$data = ['code'=>$code,'message'=>$message,'data'=>$data];
			 	$json_str = json_encode($data);
			 	return $callback."(".$json_str.")";

		 }

	} 
	
}
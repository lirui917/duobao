<?php
namespace app\goods\Controller;

use think\Request;
use think\Controller;
use app\goods\Controller\Comment;
use think\Db;
/**
 *@author  木子飞 <[email address]>
 * 
 */
 class Review extends Comment
 {
 	
 	public function rest() {
           
          switch ($this->method) {
          	case 'get':
          		$this->read($id);
          		break;
          	
          	default:
                   return self::get_msg('error','没有控制器',null);
          	    break;
          }

 	}

     /**
      * @param  [type] $id [description] 查询id
      * @return [type]     [description] 数据json
      */
 	public function read ($id=0){

          $limit = 10;            //获取多少条
          $begin = $id*$limit+1;  //开始条数  

          $data = Db::query('SELECT ecs_goods_activity.act_id,ecs_users.user_name,ecs_goods_activity.act_sum,ecs_goods_activity.act_priod,ecs_goods_activity.make_time,ecs_activity_user.act_buying FROM
     ecs_goods_activity
INNER JOIN ecs_activity_user ON ecs_activity_user.act_id = ecs_goods_activity.act_id
INNER JOIN ecs_users ON ecs_users.user_id = ecs_activity_user.act_id');
           
           // var_dump($data);die;
          if(empty($data)){
               $info = self::get_msg('error','已经没有数据',$data);
          }else{
               $info = self::get_msg('success','数据获取成功',$data);
          }
          return $info;
 	}
  
  /**
   * [teletext description]  图文详情方法
   * @param  integer $id [description]  图文详情id 
   * @return [type]      [description]  json数据
   */
  public function teletext($id=2) {
      $data = Db::name('goods')->where('goods_id',$id)->field('goods_id,goods_desc,goods_img')->select();  //商品数据
     if(empty($data))   $info = self::get_msg('error','该商品已下架，请联系客服人员',$data);
        
     else  $info = self::get_msg('success','商品获取成功',$data);
     
     return $info; 
  }



} 

<?php
namespace app\goods\controller;
use think\Controller;
use app\goods\controller\Common;
use think\Db;
use think\Request;
/**
* 夺宝评论
*/
class Ping extends Common
{
	//展示夺宝商品的全部评论
	public function sel_comment(){
		$request = Request::instance();
		$id = $request->get("id");
		$user_id = $request->get("user_id");
		//php获取今日开始时间戳和结束时间戳
	 	$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
	 	$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
	 	//php获取昨日起始时间戳和结束时间戳
	 	$beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
	 	$endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		$data = Db::table("ecs_goods_comment")->join("ecs_users","ecs_goods_comment.user_id=ecs_users.user_id")->where("ecs_goods_comment.id=".$id." and ecs_goods_comment.addtime>=".$beginYesterday." and ecs_goods_comment.addtime<=".$endToday)->select();
		$count = count($data);
		foreach ($data as $key => $value) {
			if($value['addtime']>$beginToday && $value['addtime']<$endToday){
		 		$data[$key]['addtime'] = "今天".date('H:i',$value['addtime']);
		 	}
		 	if($value['addtime']>$beginYesterday && $value['addtime']<$endYesterday){
		 		$data[$key]['addtime'] = "昨天".date('H:i',$value['addtime']);
		 	}
		}
		$data1 = Db::table("ecs_goods_like")->where("id=".$id)->select();
		foreach ($data1 as $key => $value) {
			if($value['user_id']==$user_id){
				$like['status']=1;
			}else{
				$like['status']=0;
			}
		}
		$like['count'] = count($data1);
		$res['content'] = $data;
		$res['count'] = $count;
		$res['like'] = $like;

		$this->get_msg("200","查询成功",$res);
	}
	//用户添加评论
	public function add_comment(){
		$request = Request::instance();
		$data['user_id'] = $request->get('user_id');
		$data['content'] = $request->get("content");
		$data['addtime'] = time();
		$data['id'] = $request->get('id');
		$flag = Db::table("ecs_goods_comment")->insert($data);
		if($flag==1){
			$res['error'] = 1;
			$res['content'] = "评论成功";
		}else{
			$res['error'] = 0;
			$res['content'] = "评论失败";
		}
		$this->get_msg("200","成功",$res);
	}
	//点赞
	public function add_like(){
		$request = Request::instance();
		$data['user_id'] = $request->get('user_id');
		$data['id'] = $request->get("id");
		$data['addtime'] = time();
		$flag = Db::table("ecs_goods_like")->insert($data);
		if($flag==1){
			$res['error'] = 1;
			$res['content'] = "点赞成功";
		}else{
			$res['error'] = 0;
			$res['content'] = "点赞失败";
		}
		$this->get_msg("200","成功",$res);
	}
}
?>
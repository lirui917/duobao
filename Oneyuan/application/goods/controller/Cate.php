<?php
/**
 * Created by PhpStorm.
 * User: 杲爽爽
 * Date: 2017/11/09
 * 商品分类表
 *
 */
namespace app\goods\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\goods\controller\Common;
class Cate extends Common
{
	/*
	   展示分类类型
	 */
    public function index()
    {
        $data=Db::name('category')->field(['cat_id','cat_name'])->where('parent_id',0)->select();
        if($data){
        	$this->get_msg('200','成功',$data);
        }else{
        	$this->get_msg('403','失败','');
        } 
    }
    /*
   		 根据分类搜索数据
     */

    public function search(){
    	$request=Request::instance();
    	$cat_id=$request->get('cat_id');
    	$goods=Db::name('goods')->field(['ecs_goods.goods_id','goods_name','goods_img','shop_price','exchange_integral','exchange_sum','have_exchange'])->join('exchange_goods','ecs_goods.goods_id=ecs_exchange_goods.goods_id')->where('cat_id='.$cat_id)->select();
    	if($goods){
    		$this->get_msg('200','成功',$goods);
    	}else{
        	$this->get_msg('403','失败','');
        } 
    }
    /*
    	//根据排序搜索数据
     */
    
    public function sort(){
    	$request=Request::instance();
    	$sort=$request->get('sort_type');
    	if($sort==1){
    		//最新发布
    		$order='add_time desc';
    	}elseif($sort==2){
    		//价格最低
    		$order='shop_price asc';
    	}else{
    		//价格最高
    		$order='shop_price desc';
    	}
    	$goods=Db::name('goods')->field(['ecs_goods.goods_id','goods_name','goods_img','shop_price','exchange_integral','exchange_sum','have_exchange'])->join('exchange_goods','ecs_goods.goods_id=ecs_exchange_goods.goods_id')->order($order)->select();
    	if($goods){
    		$this->get_msg('200','成功',$goods);
    	}else{
        	$this->get_msg('403','失败','');
        }
    }
     /*
    	添加晒单数据
     */
    public function form_add(){
    	$request=Request::instance();
       $file = request()->file('file');
       $sd_title=request()->get('sd_title');
       $sd_content=request()->get('sd_content');
       $act_id=request()->get('act_id');
       $order_id=request()->get('order_id');
       $user_id=1;
      // 移动到框架应用根目录/public/uploads/ 目录下
      if($file){
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $data['sd_img']='uploads/'.$info->getSaveName();
            $data['sd_title']=$sd_title;
            $data['sd_content']=$sd_content;
            $data['act_id']=$act_id;
            $data['user_id']=$user_id;
            $data['ad_time']=time();
            $data['is_sd']=1;
            Db::name('users_activity')->insert($data);
            Db::name('order_info')->where('order_id='.$order_id)->update(['is_sd'=>1]);
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
      }
    	
    }
    /*
    	展示晒单页面
     */
	public function sdfx(){
		$user_id=1;
		//未晒单
		$goods_activity_0=Db::name('order_info')->join('order_goods','ecs_order_info.order_id=order_goods.order_id')->join('goods_activity','order_goods.goods_id=goods_activity.goods_id')->join('goods','order_goods.goods_id=goods.goods_id')->field(['ecs_order_info.order_id','ecs_goods_activity.act_id','act_priod','act_name','lottery','make_time','goods_img'])->where('ecs_order_info.is_sd=0 and ecs_order_info.user_id='.$user_id)->select();
		//已晒单
		$goods_activity_1=Db::name('goods_activity')->join('goods','goods.goods_id=ecs_goods_activity.goods_id')->field(['ecs_goods_activity.act_id','act_priod','act_name','lottery','make_time','goods_img'])->select();
		//合并在一块
		$goods_activity=array('goods_activity_0'=>$goods_activity_0,'goods_activity_1'=>$goods_activity_1);
		if($goods_activity){
			$this->get_msg('200','成功',$goods_activity);
    	}else{
        	$this->get_msg('403','失败','');
        }
	}
	 /*
    	晒单的详细信息
     */
    public function sd(){
    	$user_id=1;
    	$request=Request::instance();
    	$act_id=$request->get('act_id');
    	$goods_activity=Db::name('goods_activity')->join('users_activity','ecs_goods_activity.act_id=ecs_users_activity.act_id')->join('ecs_goods','ecs_goods_activity.goods_id=ecs_goods.goods_id')->field(['sd_title','sd_img','sd_content','make_time','ecs_goods.goods_name','shop_price','goods_img'])->where('users_activity.act_id='.$act_id.' and user_id='.$user_id)->select();
    	if($goods_activity){
			$this->get_msg('200','成功',$goods_activity);
    	}else{
        	$this->get_msg('403','失败','');
        }

    }
}

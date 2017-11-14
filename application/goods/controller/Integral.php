<?php
namespace app\goods\Controller;

use think\Request;
use think\controller\Rest;  
use think\Controller;
use think\Db;


/**
 *@author 木子飞 <[email address]>
 *@param  积分兑换商品控制器
 *
 */

header('Access-Control-Allow-Origin:*'); //允许所有跨域访问 

class Integral extends Rest
{
	/**
	 * [rest description]  调用方法接口
	 * @return [type] [description]  方法
	 */
	public function rest() {


		switch ($this->method) {

			case 'get':   //查询
				$this->read($id);
				break;

			case 'post':  //新增
			     $this->add();
			     break;
			case 'put':   //修改
			     $this->update($id);
			     break;
			case 'delete': //查询
				 $this->delete($id);
				break;
			
			default:
			    $this->error('方法不存在');   //报错
				break;
		}
	} 

   /**
    * [read 查询]
    * @param  [type] $id [查询数据id]
    * @return [type]     [json查询数据]
    */
	public function read($id=null) {

           $data = Db::name('goods')->field('goods_id,shop_price,goods_number,exchange_number,goods_name,integral,goods_img')->limit(0,130)->select(); 

           $info = self::get_msg('success','数据返回成功',$data);

           $list = json_encode($info);

           return $info;
	}
   /**
    * [add 新增方法]
    */
	public function add(){
		$model = self::model();
		$param = Request::instance()->param();  //获取当前请求的所有变量（经过过滤）
		 
		 // return json($model);
		 array_shift($param);
		 // return json($param);die;
		if ($model->save($param)) {
			return json(['status'=>1]);
		}else{
			return json(['status'=>0]);
		}
	}
   
	public function update($id) {
        $model = self::model();
        $param = Request::instance()->param();
         array_shift($param);  //销毁 第一个数组
         // return json($param);die;
        if($model->where("id",$id)->update($param)){
            return json(['status'=>1]);

        }else{
        	return json(['status'=>0]);
        }
	}

	public function delete($id) {
		$model = self::model();
		$rs = $model::get($id)->delete();
		if($rs){
			return json(['status'=>1]);
		}else{
			return json(['status'=>0]);
		}
	}


	/**
	 * [get_msg description]    转换json数据
	 * @param  [type] $code     [description]  信息状态
	 * @param  [type] $message  [description]  状态信息
	 * @param  [type] $errormsg [description]  数据
	 * @return [type]           [description]  json字符串
	 */
	public static function get_msg($code,$message,$data) {

		$callback = isset($_GET['callback'])?$_GET['callback']:1;
		if(isset($callback)){
			// return json($callback); 
			$data=['code'=>$code,'message'=>$message,'data'=>$data];
			$json_str = json_encode($data);
			return $callback."(".$json_str.")";
		}
	}







}


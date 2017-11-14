<?php

namespace app\goods\Controller;

use think\Request;
use think\controller\Rest;  
use think\Controller;

/**
* 测试控制器
*/
header('Access-Control-Allow-Origin:*'); //允许所有跨域访问 

class PointsMall extends Rest
{
    
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


   /**
    * [read 查询]
    * @param  [type] $id [查询数据id]
    * @return [type]     [json查询数据]
    */
    
	public function read($id=null){

           $model =self::model();
           $data = $model->where('id',$id)->find();  //查询单个数据 
		   
		   // return json($data);die;
           $data = self::get_msg('success',$data);
           var_dump($data);die;
           return $data;
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
    * [model 私有的实例模型方法]
    * @return [type] 实例化后的模型
    */
	private static function model(){
		return model('News');
	}

	//返回错误信息
	public static function get_msg($error,$errormsg) {

		$callback = isset($_GET['callback'])?$_GET['callback']:1;
		if(isset($callback)){
			// return json($callback); 
			$data=['error'=>$error,'errormsg'=>$errormsg];
			$json_str = json_encode($data);
			return $callback."(".$json_str.")";
		}
	}



}
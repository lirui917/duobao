<?php

namespace app\goods\Controller;

use think\Request;
use think\Controller\Rest;
use think\Controller;

/**
* @author [木子飞] <[email address]>
*/

class Comment extends Rest
{
	
	function __construct()
	{
	    parent::__construct();
     
	}

/**
 * [get_msg description]   转换json数据
 * @param  [type] $code    [description]  信息状态
 * @param  [type] $message [description]  状态信息
 * @param  [type] $data    [description]  数据
 * @return [type]          [description]  json字段串
 */
	public static function get_msg($code,$message,$data) {
         
         $callback = isset($_GET['callback'])?$_GET['callback']:'';

         if(isset($callback)) {
         	$data = ['code'=>$code,'message'=>$message,'data'=>$data];
         	$json_str = json_encode($data);
         	return $callback."(".$json_str.")";

         }
	} 

}
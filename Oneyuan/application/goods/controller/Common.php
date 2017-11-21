<?php
/**
 * Created by PhpStorm.
 * User: 你们老王大哥
 * Date: 2017/11/07
 * Time: 12:05
 *
 */
namespace app\goods\controller;

use think\Controller;
use think\Request;

class Common extends Controller
{

    function __construct()
    {
        parent::__construct();
        //校验token
        $this->check_token();
    }
 
    //校验token
    public function check_token(){
        //接收 参数
        $appid=isset($_GET['appid'])?$_GET['appid']:'';
        $str=isset($_GET['str'])?$_GET['str']:'';
        $token=isset($_GET['token'])?$_GET['token']:'';
        $callback=isset($_GET['callback'])?$_GET['callback']:'';
         
        //判断是否有回调参数
        if (empty($callback)){
            $data=['code'=>500,'errormsg'=>'非法请求'];
            echo json_encode($data,JSON_UNESCAPED_UNICODE);exit;
        }
        //判断appid str 是否为空
        if (empty($appid)||empty($str)){
            $this->get_msg(401,'参数错误');
        }
        //判断token 是否存在
        if (!$token){
            $this->get_msg(402,'未找到token');
        }
        //后台生成token
        $new_token=md5($appid.'1507phpg'.$str);
       	
        //判断是否与接收到token相同
        if ($token!=$new_token){
            $this->get_msg(402,'token错误');
        }
    }

    //返回错误信息
    public function get_msg($code,$errormsg,$data){
            $callback=$_GET['callback'];
       	    $data=isset($data)?$data:'';
            $arr=['code'=>$code,'errormsg'=>$errormsg,'data'=>$data];
            
            $json_str=json_encode($arr,JSON_UNESCAPED_UNICODE);
            echo $callback."(".$json_str.")";exit;

    }
}
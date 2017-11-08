<?php
/**
 * Created by PhpStorm.
 * User: lxt
 * Date: 2017/11/07
 * Time: 12:05
 */
namespace app\index\controller;

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
        $str=isset($_GET['sign'])?$_GET['sign']:'';
        $token=isset($_GET['token'])?strtolower($_GET['token']):'';
        //判断appid str 是否为空
        if (empty($appid)||empty($str)){
            $this->get_msg('103','参数错误');
        }
        //判断token 是否存在
        if (!$token){
            $this->get_msg('102','未找到token');
        }
        //后台生成token
        $new_token=md5($appid.$str);
        //判断是否与接收到token相同
        if ($token!=$new_token){
            $this->get_msg('102','token错误');
        }
    }

    //返回错误信息
    public function get_msg($error,$errormsg){
        //判断回调方法是否存在
        if(isset($callback)){
            $callback=$_GET['callback'];
            $data=['error'=>$error,'errormsg'=>$errormsg];
            $json_str=json_encode($data);
            echo $callback."(".$json_str.")";exit;
        }else{
            $data=['error'=>'500','errormsg'=>'非法请求'];
            $json_str=json_encode($data);
            echo $json_str;exit;
        }

    }
}
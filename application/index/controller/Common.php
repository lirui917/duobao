<?php
/**
 * Created by PhpStorm.
 * User: gh
 * Date: 2017/11/07
 * Time: 12:05
 *
 */
namespace app\index\controller;

use think\Controller;
use think\Request;
header("content-type:text/html;charset=utf-8");
class Common extends Controller
{
    function __construct()
    {
        header('content-type:text/html;charset=utf-8');
        parent::__construct();
        //校验token
        $this->check_token();
    }

    /**
     * 校验token
     */
    public function check_token(){
        //接收 参数
        $appid=isset($_GET['appid'])?$_GET['appid']:'';
        $str=isset($_GET['sign'])?$_GET['sign']:'';
        $token=isset($_GET['token'])?strtolower($_GET['token']):'';
        $callback=isset($_GET['callback'])?strtolower($_GET['callback']):'';

        //判断是否有回调参数
        if (empty($callback)){
            $data=['error'=>500,'errormsg'=>'非法请求'];
            echo json_encode($data,JSON_UNESCAPED_UNICODE);exit;
        }
        //判断appid str 是否为空
        if (empty($appid)||empty($str)){
            echo $appid,$str;
            $this->get_msg('103','参数错误');
        }
        //判断token 是否存在
        if (!$token){
            $this->get_msg('102','未找到token');
        }
        //后台生成token
        $new_token=md5($appid.$str.'123');
        //判断是否与接收到token相同
        if ($token!=$new_token){
            $this->get_msg('102','token错误');
        }
    }

    /**
     * 用户输出数据
     * @param $error 错误码 int
     * @param $errormsg 错误信息 string /array
     *
     */
    public function get_msg($error,$errormsg){

        $callback=$_GET['callback'];
        $data=['error'=>$error,'errormsg'=>$errormsg];
        $json_str=json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $callback."(".$json_str.")";exit;
    }
}
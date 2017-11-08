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
        $appid=isset($_GET['appid'])?$_GET['appid']:'';
        $str=isset($_GET['sign'])?$_GET['sign']:'';
        $token=isset($_GET['token'])?strtolower($_GET['token']):'';

        if (empty($appid)||empty($str)){
            echo $this->get_msg('103','参数错误');exit;
        }

        if (!$token){
            echo $this->get_msg('102','未找到token');exit;
        }

        $new_token=md5($appid.$str);

        if ($token!=$new_token){
            echo $this->get_msg('102','token错误');exit;
        }
    }

    //返回错误信息
    public function get_msg($error,$errormsg){
        $callback=$_GET['callback'];

        $data=['error'=>$error,'errormsg'=>$errormsg];
        $json_str=json_encode($data);
        echo $callback."(".$json_str.")";exit;
    }
}
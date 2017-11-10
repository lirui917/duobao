<?php
/**
 * Created by PhpStorm.
 * User: 罗雄腾
 * Date: 2017/11/07
 * Time: 12:05
 *
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
        // $this->check_token();
    }
    //校验token
    public function check_token(){
        //接收 参数
        // echo $_GET['appid'];die;
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
    //返回错误信息
    public function get_msg($error,$errormsg){
            $callback=$_GET['callback'];
            $data=['error'=>$error,'errormsg'=>$errormsg];
            $json_str=json_encode($data,JSON_UNESCAPED_UNICODE);
            echo $callback."(".$json_str.")";exit;

    }
    public function get_rand($proArr) { //一维数组：产生概率
        $result = ''; 
        //概率数组的总概率精度 
        $proSum = array_sum($proArr); //总数为121
        // var_dump($proSum);die;
        //概率数组循环 
        
        foreach ($proArr as $key => $proCur) { //$key指的是类型
            $randNum = mt_rand(1, $proSum); //抽取随机数
            if ($randNum <= $proCur) { //1-121数< $v
                $result = $key; //result=类型                       
                break; 
            } else { 
                $proSum -= $proCur;                     
            } 
        } 
        unset ($proArr); 
        return $result; 
    }
}
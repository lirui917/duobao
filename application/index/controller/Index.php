<?php
namespace app\index\controller;

use think\Db;

class Index extends Common
{
    public function index()
    {
        return 12223;
    }

    /***
     *
     */
    public function getSellerType(){
        //查询前16条类型
        $types=Db::name('seller_type')->limit(16)->select();
        //拆分数组
        $data=array_chunk($types,8);
        if ($data){
            $this->get_msg('100',$data);
        }else{
            $this->get_msg('110','暂无数据');
        }
    }
    //通过类型id 查询店铺
    public function getSellerByType(){
        if ($_GET['typeid']){
            $type_id=$_GET['typeid'];
            $sellers=Db::name('seller')->where('seller_type',$type_id)->select();
            if ($sellers){
                foreach ($sellers as $k=>&$v){
                    $v['seller_endtime']=date('H:i:s',$v['seller_endtime']);
                    $v['seller_starttime']=date('H:i:s',$v['seller_starttime']);
                    unset($v);
                }
                $this->get_msg('100',$sellers);
            }else{
                $this->get_msg('110','暂无数据');
            }
        }else{
            $this->get_msg('500','非法请求');
        }
    }
}

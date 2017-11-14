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
     * 查询商家类型
     * @return data array 所有商家
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
    /**
     * 通过商家类型id 查询店铺
     */
    public function getSellerByType(){
        if (isset($_GET['typeid'])&&!empty($_GET['typeid'])){
            $type_id=$_GET['typeid'];   //查询分类下商家
            $sellers=Db::name('seller')->where('seller_type',$type_id)->select();
            $this->returnSellers($sellers,$type_id);
        }else if (isset($_GET['search'])&&!empty($_GET['search'])){
                $search=$this->js_unescape($_GET['search']);
                $sellers=Db::name('seller')->where('seller_name','like','%'.$search.'%')->select();
                $this->returnSellers($sellers,$type_id=false);
        }else{
            $this->get_msg('500','非法请求');
        }

    }

    /**
     * @param $str     string  经过escape 编码后的值
     * @return string   字符串 解码后的值
     */

    public function js_unescape($str){
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++)
        {
        if ($str[$i] == '%' && $str[$i+1] == 'u')
        {
        $val = hexdec(substr($str, $i+2, 4));
        if ($val < 0x7f) $ret .= chr($val);
        else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
        else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
        $i += 5;
        }
        else if ($str[$i] == '%')
        {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        }
        else $ret .= $str[$i];
        }
        return $ret;
    }

    /**
     * @param $sellers 商家数据 array
     * @param $type_id 分类id
     */

    public function returnSellers($sellers,$type_id){
        if ($sellers){
            foreach ($sellers as $k=>&$v){
                $v['seller_endtime']=date('H:i:s',$v['seller_endtime']);
                $v['seller_starttime']=date('H:i:s',$v['seller_starttime']);
                unset($v);
            }
            $data['sellers']=$sellers;
            if (isset($type_id)){
                //查询二级分类
                $category=Db::name('seller_category')->where('seller_type_id',$type_id)->select();
                if ($category){
                    $data['category']=$category;
                }
            }
            $this->get_msg('100',$data);
        }else{
            $this->get_msg('110','暂无数据');
        }
    }


    /**
     *获取商家详情 通过商家ID
     */
    public function getSellerInfoById(){
        if (isset($_GET['sellerid'])){

            $seller_id=$_GET['sellerid'];
            //查询商家详情
            $info=Db::name('seller')->where('seller_id',$seller_id)->find();

            //查询商家左边菜单
            $menus=Db::name('seller_menu')->where('seller_id',$seller_id)->select();
            if ($info){
                $info['seller_endtime']=date('H:i:s',$info['seller_endtime']);
                $info['seller_starttime']=date('H:i:s',$info['seller_starttime']);
                $data['info']=$info;
            }
            //查询首个菜单下商品
            if(!empty($menus)){
                $menu_id=isset($_GET['menu_id'])?$_GET['menu_id']:$menus[0]['menu_id'];
            }else{
                $menu_id='';
            }

            if ($menu_id){
                $goods=Db::name('goods')->where(['goods_seller_id'=>$seller_id,'menu_id'=>$menu_id])->select();
            }else{
                $goods=Db::name('goods')->where('goods_seller_id',$seller_id)->select();
            }

            if ($goods) $data['goods']=$goods;
            if($menus) $data['menus']=$menus;

            $this->get_msg('100',$data);

        }else{

            $this->get_msg('500','非法请求');
        }
    }


}

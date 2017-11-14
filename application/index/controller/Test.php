<?phpnamespace app\index\controller;

use think\Db;
use think\Controller;

class Test extends Common
{
   public function index()
   {
    echo 'ss';
   }


    public function agent()
    {
        //登录账号
        $tel=isset($_GET['tel'])?$_GET['tel']:"";
        //登录密码
        $password=isset($_GET['password'])?$_GET['password']:"";
        if($tel == ""){
            echo $this->get_msg('103','登录账号为空');exit;
        }else if($password == "" ){
            echo $this->get_msg('103','登录密码为空');exit;
        }
        $sql="select * from ecs_keywords";
        $system=Db::query($sql);
        echo $_GET['callback'].'('.json_encode($system).')';
        
    }

    
    public function detail()
    {

        //夺购商品id base64加密
        $act_id=isset($_GET['act_id'])?$_GET['act_id']:"";
        if($act_id == "MA=="){
            echo $this->get_msg('103','夺购商品id为空');exit;
        }
        //act_id base64解密
        $act_ids=base64_decode($act_id);
        $sql="SELECT act_name,act_priod,act_price,act_join,act_sum from ecs_goods_activity where act_id = $act_ids ";
        $system=Db::query($sql); 
        $system[0]['act_remain']=$system[0]['act_sum']-$system[0]['act_join'];
        //成功码
        $system[0]['error']=200;
        $system[0]['good_img']=isset($_GET['good_img'])?$_GET['good_img']:"";
        echo $_GET['callback'].'('.json_encode($system[0]).')';
        
        
    }

    /**
     * 一元夺宝
     */
    public function rush()
    {
        $sql="SELECT act_id,act_name,act_price,goods_img,start_time from ecs_goods,ecs_goods_activity where ecs_goods_activity.goods_id = ecs_goods.goods_id ";
        $goods=Db::query($sql); 
        echo $_GET['callback'].'('.json_encode($goods).')';
    }

}

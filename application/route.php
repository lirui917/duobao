<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

Route::get('/',function(){
    return 'Hello,world!';
});

Route::get('Integral/:id','goods/Integral/read');       //积分商品
Route::get('Review/:id','goods/Review/read');           //往期接口
Route::get('teletext/:id','goods/Review/teletext');     //图文详情


Route::get('pointsMall/:id','goods/pointsMall/read');       //查询
Route::post('pointsMall','goods/pointsMall/add');           //新增
Route::put('pointsMall/:id','goods/pointsMall/update');     //修改
Route::delete('pointsMall/:id','goods/pointsMall/delete');  //删除



return [
    '__pattern__' => [
        'name' => '\w+',
    ],

    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];

<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


// 使用动态注册路由
use think\Route;
// 获得用户openid
Route::get('getOpenId','index/Wechat/getUserOpenId');
Route::get('checkAccessToken','index/Wechat/checkAccessToken');
Route::get('refreshAccessToken','index/Wechat/refreshAccessToken');
Route::get('getUserInfo','index/Wechat/getUserInfo');

// 微信分享
Route::get('wechatShare','index/Wechat/wechatShare');

// 测试能砺
Route::get('getSacnSignStr','index/Wechat/getSacnSignStr');

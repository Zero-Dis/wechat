<?php
namespace app\index\controller;

use think\Request;
use think\Config;

class Index
{
    /**
     * 微信配置验证url
     * @return bool
     */
    public function index()
    {
        $data = Request::instance()->get();
        $signature = $data['signature'];
        $timestamp = $data['timestamp'];
        $nonce = $data['nonce'];
        $echostr = $data['echostr'];

        //计算微信签名
        $token = Config::get('wechat.token');
        //将参数组成一维数组
        $signeSeed = [$token, $timestamp, $nonce];
        //对参数字典序排序
        sort($signeSeed, SORT_STRING);
        //拼接成字符串
        $signeStr = implode($signeSeed);
        //加密字符串成签名
        $signeHash = sha1($signeStr);

        if ($signeHash == $signature) {
            echo $echostr;
        }else{
            return false;
        }
    }

}

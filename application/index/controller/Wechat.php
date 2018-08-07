<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 22:41
 */

namespace app\index\controller;
use think\Controller;

class Wechat extends Controller
{
    protected $accessTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token';
    protected $appId;
    protected $secret;

    /**
     * 加载微信配置
     */
    protected function _initialize(){
        $this->appId = config('wechat.appId');
        $this->secret = config('wechat.secret');
    }

    /**
     * 获取微信access_token
     * @return mixed|null
     */
    public function getAccessToken(){
        $accessToken = cache('accessToken');
        if($accessToken)
            return $accessToken;

        $param = [
            'grant_type'  =>  'client_credential',
            'appid'       =>  $this->appId,
            'secret'      =>  $this->secret,
        ];
        $result = httpGuzzle('get',$this->accessTokenUrl,$param);
        $accessToken = $result['access_token'];

        cache('accessToken',$accessToken,($result['expires_in']-10));

        return $accessToken;
    }

}
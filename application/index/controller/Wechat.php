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
    protected $wechatAuthCodeUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
    protected $userOpenIdUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
    protected $checkAccessTokenUrl = 'https://api.weixin.qq.com/sns/auth?';
    protected $refreshAccessTokenUrl = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?';

    protected $appId;
    protected $secret;
    protected $code;
    protected $openId;

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

    /**
     * 作用：格式化参数，签名过程需要使用
     * @param $paraMap
     * @param $urlencode
     * @return bool|string
     */
    protected function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    /**
     * 网页授权获取用户openId -- 1.获取授权code url
     * @param bool $isGetUserInfo
     * @return string
     */
    public function getWechatAuthCode($isGetUserInfo = false){
        // 获取来源地址
        $url = get_url();

        // 获取code
        $urlObj["appid"] = $this->appId;
        $urlObj["redirect_uri"] = "$url";
        $urlObj["response_type"] = "code";
        if(!$isGetUserInfo){
            $urlObj["scope"] = "snsapi_base";
        }else{
            $urlObj["scope"] = "snsapi_userinfo";
        }
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        $codeUrl =  $this->wechatAuthCodeUrl.$bizString;

        return $codeUrl;
    }

    /**
     * 网页授权获取用户access_token、openid
     * @return mixed
     */
    public function getUserWechatInfo(){
        if (!isset($_GET['code']))
        {
            $codeUrl = $this->getWechatAuthCode();
            Header("Location: $codeUrl");
            die;
        }else{
            $code = $_GET['code'];
            $this->code = $code;

            // 请求openid
            $param = [
                'appid'     =>  $this->appId,
                'secret'    =>  $this->secret,
                'code'      =>  $this->code,
                'grant_type'=>  "authorization_code",
            ];

            $data = httpGuzzle('get',$this->userOpenIdUrl,$param);
            cache('wechatUserInfo',$data,$data['expires_in']?($data['expires_in']-10):7190);
            return $data;
        }
    }

    /**
     * 网页授权获取用户openId -- 2.获取openid
     * @return mixed
     */
    public function getUserOpenId(){
        $wechatUserInfo = cache('wechatUserInfo');
        if($wechatUserInfo){
            //取出openid
            $this->openId = $wechatUserInfo['openid'];
        }else{
            $wechatUserInfoNew = $this->getUserWechatInfo();
            $this->openId = $wechatUserInfoNew['openid'];
        }
        return $this->openId;
    }

    /**
     * 检验授权凭证（access_token）是否有效
     * @return bool
     */
    public function checkAccessToken(){
        $this->getWechatAuthCode();
        $param = [
            'access_token' => cache('wechatUserInfo')['access_token'],
            'openid'       => $this->getUserOpenId()
        ];

        $check = httpGuzzle('get',$this->checkAccessTokenUrl,$param);
        if($check['errcode'] == 0 && $check['errmsg'] == 'ok')
            return true;
        // 刷新 access_token
        $this->refreshAccessToken();
    }

    /**
     * 刷新access_token
     * @return bool
     */
    public function refreshAccessToken(){
        $param = [
            'appid'         =>    $this->appId,
            'grant_type'    =>    'refresh_token',
            'refresh_token' =>    cache('wechatUserInfo')['refresh_token'],
        ];
        $refresh = httpGuzzle('get',$this->refreshAccessTokenUrl,$param);
        cache('wechatUserInfo',$refresh,$refresh['expires_in']?($refresh['expires_in']-10):7190);
        if(!in_array('errcode',$refresh))
            return true;
        return false;
    }


    public function getUserInfo(){

    }

}
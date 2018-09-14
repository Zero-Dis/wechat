<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 封装 httpGuzzle
 * @param $curlType
 * @param $url
 * @param $data
 * @return mixed
 */
function httpGuzzle($curlType,$url,$data)
{
    $curlType = strtoupper($curlType);
    switch ($curlType) {
        case 'GET':
            $config['query'] = $data;
            break;
        case 'POST':
            $config['form_params'] = $data;
            break;
        default:
            # code...
            break;
    }
    $config['verify'] = false;

    $client = new \GuzzleHttp\Client();
    $respose = $client->request($curlType,$url,$config);
    $result = json_decode($respose->getBody(),true);
    return $result;
}

/**
 * 获取来源地址
 * @return string
 */
function get_url() {
    //获取来源地址
    $url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    return $url;
}

// ----------------------------------------------------------

function genRandomString($len = 32) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z"
    );
    $charsLen = count($chars) - 1;
    // 将数组打乱
    shuffle($chars);
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
 * GET 请求
 * @param string $url
 */
function http_get($url)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return json_decode($sContent,true);
    } else {
        return false;
    }
}

/**
 * [getAccessToken 获取基础token,统一保存,全局使用,有效期2小时]
 * @return [type] [description]
 */
function getAccessToken($appid='',$appsecret='')
{
    //缓存取数据
    /*$access_data = S('access_token');
    if(!$access_data  || $access_data ['expires_time']<=time()){*/
    //请求接口
    $url    = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
    $result = http_get($url);
    if(!$result)
        return false;

    //储存token
    /*$result['expires_time'] = time()+7190;
    S('access_token',$result,$result['expires_time']);*/

    $access_data = $result;
    // }
    $access_token = $access_data['access_token'] ? $access_data['access_token'] : '';

    return $access_token;
}
/**
 * [getWxJsapiTicket 获取jsapi_ticket,使用基础token]
 * @param  [type] $openid [description]
 * @return [type]         [description]
 */
function getWxJsapiTicket($appid='',$appsecret='')
{
    //缓存取数据
    /*$jsapi_ticket_data = S('jsapi_ticket');
    if(!$jsapi_ticket_data  || $jsapi_ticket_data['expires_time']<=time()){*/
    //获取token
    $access_token = getAccessToken($appid,$appsecret);
    if(!$access_token)
        return false;

    //获取用户信息
    $url    = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
    $result = http_get($url);
    if(!$result)
        return false;

    /*if(isset($result['errcode']) && empty($result['errcode']) && isset($result['errmsg']) && in_array($result['errmsg'], ['ok','OK']) ){
        //储存jsapi_ticket
        $result['expires_time'] = time()+7190;
        S('jsapi_ticket',$result,$result['expires_time']);
    }*/
    $jsapi_ticket_data = $result;
    // }

    //储存jsapi_ticket
    $ticket = $jsapi_ticket_data['ticket'] ? $jsapi_ticket_data['ticket'] : '';

    return $ticket;
}


function makeSign( $params ){
    //签名步骤一：按字典序排序数组参数
    ksort($params);

    //签名步骤二：拼接成字符串
    $string = toUrlParams($params);

    //签名步骤三：sha1加密
    $string = Sha1($string);
    return $string;
}

/**
 * 将参数拼接为url: key=value&key=value
 * @param   $params
 * @return  string
 */
function toUrlParams($params){
    $string = '';
    if( !empty($params) ){
        foreach( $params as $key => $value ){
            //校验空值
            if(!checkEmpty($value)){
                $string .= $key ."=". $value . "&";
            }
        }
        $string = trim($string, '&');
    }

    return $string;
}

function checkEmpty($value) {
    if (!isset($value))
        return true;
    if ($value === null)
        return true;
    if (trim($value) === "")
        return true;
    if ($value === [])
        return true;

    return false;
}
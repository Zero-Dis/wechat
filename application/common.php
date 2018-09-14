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

/**
 * 生成随机字符串
 * @param int $len
 * @return string
 */
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








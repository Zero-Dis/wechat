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
    $url = "https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    return $url;
}
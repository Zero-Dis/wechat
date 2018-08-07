<?php
namespace app\index\controller;

class Index
{
    public function index()
    {

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];
        halt($_GET);
        $tmpArr = array($timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $signature == $tmpStr ){
            return true;
        }else{
            return false;
        }
    }
}

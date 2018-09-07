<?php

namespace edrard\Curl;

use edrard\Curl\Agents;

/**
* Get Url using based PHP functions with retry and pauses;
*/

class GetUrl
{
    public static function getSingle($url, $retry, Callable $function = NULL){
        $res = FALSE;
        while( $retry >= 0 && $res === FALSE) 
        {
            if(!$url){
                $res = '';
                break;
            } 
            $sleep = $function !== NULL ? $function($retry,$url) : 1;
            sleep($sleep);
            $ctx = stream_context_create(array('http'=>
                array(
                    'timeout' => 15, // 1 200 Seconds = 20 Minutes
                )
            ));  
            $res = file_get_contents($url, false, $ctx);  
            $retry--;
        }
        return $res;
    }
    public static function getSingleCurl($url, $retry, Callable $function = NULL)
    {
        $ch = curl_init(); 
        curl_setopt( $ch, CURLOPT_USERAGENT, Agents::get()); 
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);        
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true);     
        curl_setopt( $ch, CURLOPT_TIMEOUT, 8); 
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt( $ch, CURLOPT_URL,$url); 
        $res = FALSE;
        while( $retry >= 0 && $res === FALSE) 
        {
            if(!$url){
                $res = '';
                break;
            } 
            $sleep = $function !== NULL ? $function($retry,$url) : 1;
            sleep($sleep);
            $res = curl_exec( $ch );
            $code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            if ( $code[0] < 0 || $code[0] > 400 ){
                $res = FALSE;
            }
            $retry--;
        }
        return $res;
    } 
}